<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Lib\Metrics\Inspection;

use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection\AbstractInspection;
use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection\BelowCustomCoverageInspection;
use DigitalRevolution\CodeCoverageInspection\Model\Config\FileInspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection\BelowCustomCoverageInspection
 * @covers \DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection\AbstractInspection::__construct
 */
class BelowCustomCoverageInspectionTest extends TestCase
{
    /** @var BelowCustomCoverageInspection */
    private $inspection;

    protected function setUp(): void
    {
        $config     = new InspectionConfig('/tmp/b', 80);
        $this->inspection = new BelowCustomCoverageInspection($config);
    }

    /**
     * @covers ::inspect
     */
    public function testInspectNoCustomCoverageShouldPass(): void
    {
        static::assertNull($this->inspection->inspect(null, new FileMetric('/tmp/b/', 0, [])));
    }

    /**
     * Custom coverage 40%
     * @covers ::inspect
     */
    public function testInspectCoverageBelowCustomCoverageShouldFail(): void
    {
        $fileConfig = new FileInspectionConfig('/tmp/b', 40);
        $metric     = new FileMetric('/tmp/b', 20, []);

        $failure = $this->inspection->inspect($fileConfig, $metric);
        static::assertNotNull($failure);
        static::assertSame(Failure::CUSTOM_COVERAGE_TOO_LOW, $failure->getReason());
        static::assertSame(40, $failure->getMinimumCoverage());
    }

    /**
     * @covers ::inspect
     */
    public function testInspectCoverageAboveCustomCoverageShouldPass(): void
    {
        $fileConfig = new FileInspectionConfig('/tmp/b', 40);
        $metric     = new FileMetric('/tmp/b', 60, []);

        static::assertNull($this->inspection->inspect($fileConfig, $metric));
    }
}
