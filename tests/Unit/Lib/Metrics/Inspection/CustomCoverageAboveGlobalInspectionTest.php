<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Lib\Metrics\Inspection;

use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection\CustomCoverageAboveGlobalInspection;
use DigitalRevolution\CodeCoverageInspection\Model\Config\PathInspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection\CustomCoverageAboveGlobalInspection
 * @covers \DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection\AbstractInspection::__construct
 */
class CustomCoverageAboveGlobalInspectionTest extends TestCase
{
    private CustomCoverageAboveGlobalInspection $inspection;

    protected function setUp(): void
    {
        $config           = new InspectionConfig('/tmp/', 80);
        $this->inspection = new CustomCoverageAboveGlobalInspection($config);
    }

    /**
     * @covers ::inspect
     */
    public function testInspectNoCustomCoverageShouldPass(): void
    {
        static::assertNull($this->inspection->inspect(null, new FileMetric('/tmp/b/', 0, [])));
    }

    /**
     * @covers ::inspect
     */
    public function testInspectCoverageBelowGlobalCoverageShouldPass(): void
    {
        $fileConfig = new PathInspectionConfig(PathInspectionConfig::TYPE_FILE, '/tmp/b', 40);
        $metric     = new FileMetric('/tmp/b', 70, []);

        static::assertNull($this->inspection->inspect($fileConfig, $metric));
    }

    /**
     * @covers ::inspect
     */
    public function testInspectCoverageAboveGlobalCoverageShouldFail(): void
    {
        $fileConfig = new PathInspectionConfig(PathInspectionConfig::TYPE_FILE, '/tmp/b', 40);
        $metric     = new FileMetric('/tmp/b', 90, []);

        $failure = $this->inspection->inspect($fileConfig, $metric);
        static::assertNotNull($failure);
        static::assertSame(Failure::UNNECESSARY_CUSTOM_COVERAGE, $failure->getReason());
        static::assertSame(40, $failure->getMinimumCoverage());
    }

    /**
     * @covers ::inspect
     */
    public function testInspectCoverageCustomCoverageAboveGlobalCoverageShouldPass(): void
    {
        // global is 80
        $fileConfig = new PathInspectionConfig(PathInspectionConfig::TYPE_FILE, '/tmp/b', 85);
        $metric     = new FileMetric('/tmp/b', 83, []);

        static::assertNull($this->inspection->inspect($fileConfig, $metric));
    }
}
