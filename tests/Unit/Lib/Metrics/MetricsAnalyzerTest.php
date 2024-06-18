<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Lib\Metrics;

use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\MetricsAnalyzer;
use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Config\PathInspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\MethodMetric;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MetricsAnalyzer::class)]
class MetricsAnalyzerTest extends TestCase
{
    public function testAnalyzeFileAboveMinimumShouldPass(): void
    {
        $metrics[] = new FileMetric('/a/b/c/test.php', 0, 80, [], []);
        $config    = new InspectionConfig('/a/', 80);

        $analyzer = new MetricsAnalyzer($metrics, $config);
        $result   = $analyzer->analyze();
        static::assertEmpty($result);
    }

    public function testAnalyzeFileBelowMinimumShouldFail(): void
    {
        $metric  = new FileMetric('/a/b/c/test.php', 0, 79.4, [], []);
        $metrics = [$metric];
        $config  = new InspectionConfig('/a/', 80);

        $analyzer = new MetricsAnalyzer($metrics, $config);
        $result   = $analyzer->analyze();
        static::assertCount(1, $result);
        static::assertEquals([new Failure($metric, 80, Failure::GLOBAL_COVERAGE_TOO_LOW)], $result);
    }

    public function testAnalyzeFileWithCustomCoverageRuleShouldPass(): void
    {
        $metrics[] = new FileMetric('/a/b/c/test.php', 0, 45, [], []);
        $config    = new InspectionConfig('/a/', 80, false);
        $config->addPathInspection(new PathInspectionConfig(PathInspectionConfig::TYPE_FILE, 'b/c/test.php', 40));

        $analyzer = new MetricsAnalyzer($metrics, $config);
        $result   = $analyzer->analyze();
        static::assertEmpty($result);
    }

    public function testAnalyzeFileWithCustomCoverageRuleShouldFail(): void
    {
        $metric  = new FileMetric('/a/b/c/test.php', 0, 45, [], []);
        $metrics = [$metric];
        $config  = new InspectionConfig('/a/', 80, false);
        $config->addPathInspection(new PathInspectionConfig(PathInspectionConfig::TYPE_FILE, 'b/c/test.php', 50));

        $analyzer = new MetricsAnalyzer($metrics, $config);
        $result   = $analyzer->analyze();
        static::assertCount(1, $result);
        static::assertEquals([new Failure($metric, 50, Failure::CUSTOM_COVERAGE_TOO_LOW)], $result);
    }

    public function testAnalyzeFileWithCustomCoverageAboveGlobalCoverageShouldFail(): void
    {
        $metric  = new FileMetric('/a/b/c/test.php', 0, 90, [], []);
        $metrics = [$metric];
        $config  = new InspectionConfig('/a/', 80, false);
        $config->addPathInspection(new PathInspectionConfig(PathInspectionConfig::TYPE_FILE, 'b/c/test.php', 50));

        $analyzer = new MetricsAnalyzer($metrics, $config);
        $result   = $analyzer->analyze();
        static::assertCount(1, $result);
        static::assertEquals([new Failure($metric, 50, Failure::UNNECESSARY_CUSTOM_COVERAGE)], $result);
    }

    public function testAnalyzeFileWithUncoveredMethodsShouldFail(): void
    {
        $metric    = new FileMetric('/a/b/c/test.php', 0, 80, [new MethodMetric('foobar', 10, 0)], []);
        $metrics[] = $metric;
        $config    = new InspectionConfig('/a/', 80, false);

        $analyzer = new MetricsAnalyzer($metrics, $config);
        $result   = $analyzer->analyze();
        static::assertCount(1, $result);
        static::assertEquals([new Failure($metric, 80, Failure::MISSING_METHOD_COVERAGE, 10)], $result);
    }

    public function testAnalyzeFileWithoutAnyUncoveredMethodsShouldPass(): void
    {
        $metric    = new FileMetric('/a/b/c/test.php', 0, 80, [new MethodMetric('foobar', 10, 20)], []);
        $metrics[] = $metric;
        $config    = new InspectionConfig('/a/', 80, false);

        $analyzer = new MetricsAnalyzer($metrics, $config);
        static::assertEmpty($analyzer->analyze());
    }

    public function testAnalyzeFileWithUncoveredMethodsButAllowedShouldPass(): void
    {
        $metric    = new FileMetric('/a/b/c/test.php', 0, 80, [new MethodMetric('foobar', 10, 0)], []);
        $metrics[] = $metric;
        $config    = new InspectionConfig('/a/', 80, true);

        $analyzer = new MetricsAnalyzer($metrics, $config);
        static::assertEmpty($analyzer->analyze());
    }
}
