<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Lib\Metrics\Inspection;

use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection\AbstractInspection;
use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection\BelowGlobalCoverageInspection;
use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Config\PathInspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BelowGlobalCoverageInspection::class)]
#[CoversClass(AbstractInspection::class)]
class BelowGlobalCoverageInspectionTest extends TestCase
{
    private BelowGlobalCoverageInspection $inspection;

    protected function setUp(): void
    {
        $config           = new InspectionConfig('/tmp/', 80);
        $this->inspection = new BelowGlobalCoverageInspection($config);
    }

    public function testInspectCustomCoverageShouldPass(): void
    {
        $fileConfig = new PathInspectionConfig(PathInspectionConfig::TYPE_FILE, '/tmp/a', 20);
        static::assertNull($this->inspection->inspect($fileConfig, new FileMetric('/tmp/a/', 0, 20, [], [])));
    }

    public function testInspectAboveGlobalCoverageShouldPass(): void
    {
        static::assertNull($this->inspection->inspect(null, new FileMetric('/tmp/a/', 0, 90, [], [])));
    }

    public function testInspectBelowGlobalCoverageShouldFail(): void
    {
        $failure = $this->inspection->inspect(null, new FileMetric('/tmp/a/', 0, 70, [], []));
        static::assertNotNull($failure);
        static::assertSame(Failure::GLOBAL_COVERAGE_TOO_LOW, $failure->getReason());
        static::assertSame(80, $failure->getMinimumCoverage());
    }
}
