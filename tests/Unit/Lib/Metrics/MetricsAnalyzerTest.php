<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Lib\Metrics;

use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\MetricsAnalyzer;
use DigitalRevolution\CodeCoverageInspection\Model\Config\FileInspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DigitalRevolution\CodeCoverageInspection\Lib\Metrics\MetricsAnalyzer
 * @covers ::__construct
 */
class MetricsAnalyzerTest extends TestCase
{
    /**
     * @covers ::analyze
     */
    public function testAnalyzeFileAboveMinimumShouldPass(): void
    {
        $metrics[] = new FileMetric('/a/b/c/test.php', 80, []);
        $config    = new InspectionConfig('/a/', 80);

        $analyzer = new MetricsAnalyzer($metrics, $config);
        $result   = $analyzer->analyze();
        static::assertEmpty($result);
    }

    /**
     * @covers ::analyze
     */
    public function testAnalyzeFileBelowMinimumShouldFail(): void
    {
        $metric  = new FileMetric('/a/b/c/test.php', 79.4, []);
        $metrics = [$metric];
        $config  = new InspectionConfig('/a/', 80);

        $analyzer = new MetricsAnalyzer($metrics, $config);
        $result   = $analyzer->analyze();
        static::assertCount(1, $result);
        static::assertEquals([new Failure($metric, 80, Failure::GLOBAL_COVERAGE_TOO_LOW)], $result);
    }

    /**
     * @covers ::analyze
     */
    public function testAnalyzeFileWithCustomCoverageRuleShouldPass(): void
    {
        $metrics[] = new FileMetric('/a/b/c/test.php', 45, []);
        $config    = new InspectionConfig('/a/', 80, false, ['b/c/test.php' => new FileInspectionConfig('b/c/test.php', 40)]);

        $analyzer = new MetricsAnalyzer($metrics, $config);
        $result   = $analyzer->analyze();
        static::assertEmpty($result);
    }

    /**
     * @covers ::analyze
     */
    public function testAnalyzeFileWithCustomCoverageRuleShouldFail(): void
    {
        $metric  = new FileMetric('/a/b/c/test.php', 45, []);
        $metrics = [$metric];
        $config  = new InspectionConfig('/a/', 80, false, ['b/c/test.php' => new FileInspectionConfig('b/c/test.php', 50)]);

        $analyzer = new MetricsAnalyzer($metrics, $config);
        $result   = $analyzer->analyze();
        static::assertCount(1, $result);
        static::assertEquals([new Failure($metric, 50, Failure::CUSTOM_COVERAGE_TOO_LOW)], $result);
    }

    /**
     * @covers ::analyze
     */
    public function testAnalyzeFileWithCustomCoverageAboveGlobalCoverageShouldFail(): void
    {
        $metric  = new FileMetric('/a/b/c/test.php', 90, []);
        $metrics = [$metric];
        $config  = new InspectionConfig('/a/', 80, false, ['b/c/test.php' => new FileInspectionConfig('b/c/test.php', 50)]);

        $analyzer = new MetricsAnalyzer($metrics, $config);
        $result   = $analyzer->analyze();
        static::assertCount(1, $result);
        static::assertEquals([new Failure($metric, 50, Failure::UNNECESSARY_CUSTOM_COVERAGE)], $result);
    }
}
