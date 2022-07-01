<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Lib\Metrics;

use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\MetricsAnalyzer;
use DigitalRevolution\CodeCoverageInspection\Model\Config\PathInspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\MethodMetric;
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
        $config    = new InspectionConfig('/a/', 80, false);
        $config->addPathInspection(new PathInspectionConfig(PathInspectionConfig::TYPE_FILE, 'b/c/test.php', 40));

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
        $config  = new InspectionConfig('/a/', 80, false);
        $config->addPathInspection(new PathInspectionConfig(PathInspectionConfig::TYPE_FILE, 'b/c/test.php', 50));

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
        $config  = new InspectionConfig('/a/', 80, false);
        $config->addPathInspection(new PathInspectionConfig(PathInspectionConfig::TYPE_FILE, 'b/c/test.php', 50));

        $analyzer = new MetricsAnalyzer($metrics, $config);
        $result   = $analyzer->analyze();
        static::assertCount(1, $result);
        static::assertEquals([new Failure($metric, 50, Failure::UNNECESSARY_CUSTOM_COVERAGE)], $result);
    }

    /**
     * @covers ::analyze
     */
    public function testAnalyzeFileWithUncoveredMethodsShouldFail(): void
    {
        $metric    = new FileMetric('/a/b/c/test.php', 80, [new MethodMetric('foobar', 10, 0)]);
        $metrics[] = $metric;
        $config    = new InspectionConfig('/a/', 80, false);

        $analyzer = new MetricsAnalyzer($metrics, $config);
        $result   = $analyzer->analyze();
        static::assertCount(1, $result);
        static::assertEquals([new Failure($metric, 80, Failure::MISSING_METHOD_COVERAGE, 10)], $result);
    }

    /**
     * @covers ::analyze
     */
    public function testAnalyzeFileWithoutAnyUncoveredMethodsShouldPass(): void
    {
        $metric    = new FileMetric('/a/b/c/test.php', 80, [new MethodMetric('foobar', 10, 20)]);
        $metrics[] = $metric;
        $config    = new InspectionConfig('/a/', 80, false);

        $analyzer = new MetricsAnalyzer($metrics, $config);
        static::assertEmpty($analyzer->analyze());
    }

    /**
     * @covers ::analyze
     */
    public function testAnalyzeFileWithUncoveredMethodsButAllowedShouldPass(): void
    {
        $metric    = new FileMetric('/a/b/c/test.php', 80, [new MethodMetric('foobar', 10, 0)]);
        $metrics[] = $metric;
        $config    = new InspectionConfig('/a/', 80, true);

        $analyzer = new MetricsAnalyzer($metrics, $config);
        static::assertEmpty($analyzer->analyze());
    }
}
