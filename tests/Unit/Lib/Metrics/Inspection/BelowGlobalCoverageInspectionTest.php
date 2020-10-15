<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Lib\Metrics\Inspection;

use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection\BelowCustomCoverageInspection;
use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection\BelowGlobalCoverageInspection;
use DigitalRevolution\CodeCoverageInspection\Model\Config\FileInspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection\BelowGlobalCoverageInspection
 * @covers \DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection\AbstractInspection::__construct
 */
class BelowGlobalCoverageInspectionTest extends TestCase
{
    /** @var BelowCustomCoverageInspection */
    private $inspection;

    protected function setUp(): void
    {
        $config           = new InspectionConfig('/tmp/', 80);
        $this->inspection = new BelowGlobalCoverageInspection($config);
    }

    /**
     * @covers ::inspect
     */
    public function testInspectCustomCoverageShouldPass(): void
    {
        $fileConfig = new FileInspectionConfig('/tmp/a', 20);
        static::assertNull($this->inspection->inspect($fileConfig, new FileMetric('/tmp/a', 20, [])));
    }

    /**
     * @covers ::inspect
     */
    public function testInspectAboveGlobalCoverageShouldPass(): void
    {
        static::assertNull($this->inspection->inspect(null, new FileMetric('/tmp/a', 90, [])));
    }

    /**
     * @covers ::inspect
     */
    public function testInspectBelowGlobalCoverageShouldFail(): void
    {
        $failure = $this->inspection->inspect(null, new FileMetric('/tmp/a', 70, []));
        static::assertNotNull($failure);
        static::assertSame(Failure::GLOBAL_COVERAGE_TOO_LOW, $failure->getReason());
        static::assertSame(80, $failure->getMinimumCoverage());
    }
}
