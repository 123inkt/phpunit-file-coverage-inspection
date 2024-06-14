<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Lib\Metrics\Inspection;

use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection\AbstractInspection;
use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection\BelowCustomCoverageInspection;
use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Config\PathInspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BelowCustomCoverageInspection::class)]
#[CoversClass(AbstractInspection::class)]
class BelowCustomCoverageInspectionTest extends TestCase
{
    private BelowCustomCoverageInspection $inspection;

    protected function setUp(): void
    {
        $config           = new InspectionConfig('/tmp/', 80);
        $this->inspection = new BelowCustomCoverageInspection($config);
    }

    public function testInspectNoCustomCoverageShouldPass(): void
    {
        static::assertNull($this->inspection->inspect(null, new FileMetric('/tmp/a/', 0, 0, [], [])));
    }

    /**
     * Custom coverage 40%
     */
    public function testInspectCoverageBelowCustomCoverageShouldFail(): void
    {
        $fileConfig = new PathInspectionConfig(PathInspectionConfig::TYPE_FILE, '/tmp/b', 40);
        $metric     = new FileMetric('/tmp/a/', 0, 20, [], []);

        $failure = $this->inspection->inspect($fileConfig, $metric);
        static::assertNotNull($failure);
        static::assertSame(Failure::CUSTOM_COVERAGE_TOO_LOW, $failure->getReason());
        static::assertSame(40, $failure->getMinimumCoverage());
    }

    public function testInspectCoverageAboveCustomCoverageShouldPass(): void
    {
        $fileConfig = new PathInspectionConfig(PathInspectionConfig::TYPE_FILE, '/tmp/b', 40);
        $metric     = new FileMetric('/tmp/a/', 0, 60, [], []);

        static::assertNull($this->inspection->inspect($fileConfig, $metric));
    }
}
