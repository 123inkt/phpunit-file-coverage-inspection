<?php
declare(strict_types=1);

namespace DR\CodeCoverageInspection\Tests\Unit\Lib\Metrics;

use DR\CodeCoverageInspection\Lib\Metrics\MetricsAnalyzer;
use DR\CodeCoverageInspection\Model\Config\FileInspectionConfig;
use DR\CodeCoverageInspection\Model\Config\InspectionConfig;
use DR\CodeCoverageInspection\Model\Metric\Failure;
use DR\CodeCoverageInspection\Model\Metric\Metric;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DR\CodeCoverageInspection\Lib\Metrics\MetricsAnalyzer
 * @covers ::__construct
 */
class MetricsAnalyzerTest extends TestCase
{
    /**
     * @covers ::analyze
     */
    public function testAnalyzeFileAboveMinimumShouldPass(): void
    {
        $metrics[] = new Metric('/a/b/c/test.php', 80);
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
        $metric  = new Metric('/a/b/c/test.php', 79.4);
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
        $metrics[] = new Metric('/a/b/c/test.php', 45);
        $config    = new InspectionConfig('/a/', 80, ['b/c/test.php' => new FileInspectionConfig('b/c/test.php', 40)]);

        $analyzer = new MetricsAnalyzer($metrics, $config);
        $result   = $analyzer->analyze();
        static::assertEmpty($result);
    }

    /**
     * @covers ::analyze
     */
    public function testAnalyzeFileWithCustomCoverageRuleShouldFail(): void
    {
        $metric  = new Metric('/a/b/c/test.php', 45);
        $metrics = [$metric];
        $config  = new InspectionConfig('/a/', 80, ['b/c/test.php' => new FileInspectionConfig('b/c/test.php', 50)]);

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
        $metric  = new Metric('/a/b/c/test.php', 90);
        $metrics = [$metric];
        $config  = new InspectionConfig('/a/', 80, ['b/c/test.php' => new FileInspectionConfig('b/c/test.php', 50)]);

        $analyzer = new MetricsAnalyzer($metrics, $config);
        $result   = $analyzer->analyze();
        static::assertCount(1, $result);
        static::assertEquals([new Failure($metric, 50, Failure::UNNECESSARY_CUSTOM_COVERAGE)], $result);
    }
}
