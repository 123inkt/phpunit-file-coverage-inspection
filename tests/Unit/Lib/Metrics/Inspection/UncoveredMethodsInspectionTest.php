<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Lib\Metrics\Inspection;

use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection\UncoveredMethodsInspection;
use DigitalRevolution\CodeCoverageInspection\Model\Config\FileInspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\MethodMetric;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection\UncoveredMethodsInspection
 * @covers \DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection\AbstractInspection::__construct
 */
class UncoveredMethodsInspectionTest extends TestCase
{
    /** @var UncoveredMethodsInspection */
    private $inspection;

    protected function setUp(): void
    {
        $config           = new InspectionConfig('/tmp/', 80);
        $this->inspection = new UncoveredMethodsInspection($config);
    }

    /**
     * @covers ::inspect
     */
    public function testInspectCustomCoverageShouldPass(): void
    {
        $fileConfig = new FileInspectionConfig('/tmp/b', 40);
        $metric     = new FileMetric('/tmp/b', 20, []);

        static::assertNull($this->inspection->inspect($fileConfig, $metric));
    }


    /**
     * @covers ::inspect
     */
    public function testInspectNoUncoveredMethodsShouldPass(): void
    {
        $metric = new FileMetric('/tmp/b', 20, [new MethodMetric('foobar', 200, 10)]);

        static::assertNull($this->inspection->inspect(null, $metric));
    }

    /**
     * @covers ::inspect
     */
    public function testInspectUncoveredMethodsShouldFail(): void
    {
        $metric = new FileMetric('/tmp/b', 20, [new MethodMetric('foobar', 200, 0)]);

        $failure = $this->inspection->inspect(null, $metric);
        static::assertNotNull($failure);
        static::assertSame(Failure::MISSING_METHOD_COVERAGE, $failure->getReason());
        static::assertSame(80, $failure->getMinimumCoverage());
        static::assertSame(200, $failure->getLineNumber());
    }
}
