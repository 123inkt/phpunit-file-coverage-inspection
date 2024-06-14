<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Lib\Metrics\Inspection;

use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection\AbstractInspection;
use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection\UncoveredMethodsInspection;
use DigitalRevolution\CodeCoverageInspection\Model\Config\IgnoreUncoveredMethodFile;
use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Config\PathInspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\MethodMetric;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection\UncoveredMethodsInspection::class)]
#[CoversClass(AbstractInspection::__construct)]
class UncoveredMethodsInspectionTest extends TestCase
{
    private InspectionConfig $config;
    private UncoveredMethodsInspection $inspection;

    protected function setUp(): void
    {
        $this->config     = new InspectionConfig('/tmp/', 80);
        $this->inspection = new UncoveredMethodsInspection($this->config);
    }

    public function testInspectCustomCoverageShouldPass(): void
    {
        $fileConfig = new PathInspectionConfig(PathInspectionConfig::TYPE_FILE, '/tmp/b', 40);
        $metric     = new FileMetric('/tmp/b', 0, 20, [], []);

        static::assertNull($this->inspection->inspect($fileConfig, $metric));
    }

    public function testInspectNoUncoveredMethodsShouldPass(): void
    {
        $metric = new FileMetric('/tmp/b', 0, 20, [new MethodMetric('foobar', 200, 10)], []);

        static::assertNull($this->inspection->inspect(null, $metric));
    }

    public function testInspectUncoveredMethodsShouldFail(): void
    {
        $metric = new FileMetric('/tmp/b', 0, 20, [new MethodMetric('foobar', 200, 0)], []);

        $failure = $this->inspection->inspect(null, $metric);
        static::assertNotNull($failure);
        static::assertSame(Failure::MISSING_METHOD_COVERAGE, $failure->getReason());
        static::assertSame(80, $failure->getMinimumCoverage());
        static::assertSame(200, $failure->getLineNumber());
    }

    public function testInspectIgnoredUncoveredMethodShouldPass(): void
    {
        $metric = new FileMetric('/tmp/b', 0, 20, [new MethodMetric('foobar', 200, 0)], []);
        $this->config->addIgnoreUncoveredMethodFile(new IgnoreUncoveredMethodFile('/tmp/b'));

        static::assertNull($this->inspection->inspect(null, $metric));
    }
}
