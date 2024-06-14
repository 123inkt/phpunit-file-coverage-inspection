<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Lib\Metrics\Inspection;

use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection\AbstractInspection;
use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection\CustomCoverageAboveGlobalInspection;
use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Config\PathInspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CustomCoverageAboveGlobalInspection::class)]
#[CoversClass(AbstractInspection::class)]
class CustomCoverageAboveGlobalInspectionTest extends TestCase
{
    private CustomCoverageAboveGlobalInspection $inspection;

    protected function setUp(): void
    {
        $config           = new InspectionConfig('/tmp/', 80);
        $this->inspection = new CustomCoverageAboveGlobalInspection($config);
    }

    public function testInspectNoCustomCoverageShouldPass(): void
    {
        static::assertNull($this->inspection->inspect(null, new FileMetric('/tmp/b/', 0, 0, [], [])));
    }

    public function testInspectCoverageBelowGlobalCoverageShouldPass(): void
    {
        $fileConfig = new PathInspectionConfig(PathInspectionConfig::TYPE_FILE, '/tmp/b', 40);
        $metric     = new FileMetric('/tmp/b/', 0, 70, [], []);

        static::assertNull($this->inspection->inspect($fileConfig, $metric));
    }

    public function testInspectCoverageAboveGlobalCoverageShouldFail(): void
    {
        $fileConfig = new PathInspectionConfig(PathInspectionConfig::TYPE_FILE, '/tmp/b', 40);
        $metric     = new FileMetric('/tmp/b/', 0, 90, [], []);

        $failure = $this->inspection->inspect($fileConfig, $metric);
        static::assertNotNull($failure);
        static::assertSame(Failure::UNNECESSARY_CUSTOM_COVERAGE, $failure->getReason());
        static::assertSame(40, $failure->getMinimumCoverage());
    }

    public function testInspectCoverageCustomCoverageAboveGlobalCoverageShouldPass(): void
    {
        // global is 80
        $fileConfig = new PathInspectionConfig(PathInspectionConfig::TYPE_FILE, '/tmp/b', 85);
        $metric     = new FileMetric('/tmp/b/', 0, 83, [], []);

        static::assertNull($this->inspection->inspect($fileConfig, $metric));
    }
}
