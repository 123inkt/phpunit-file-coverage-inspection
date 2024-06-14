<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Lib\Metrics;

use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\FileMetricAnalyzer;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\MethodMetric;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\DigitalRevolution\CodeCoverageInspection\Lib\Metrics\FileMetricAnalyzer::class)]
class FileMetricAnalyzerTest extends TestCase
{
    public function testGetUncoveredMethodMetricShouldReturnNullForFileWithoutMethods(): void
    {
        $fileMetric = new FileMetric('/tmp/a/', 0, 20, [], []);

        static::assertNull(FileMetricAnalyzer::getUncoveredMethodMetric($fileMetric));
    }

    public function testGetUncoveredMethodMetricShouldReturnMethodWithoutCoverage(): void
    {
        $metricA    = new MethodMetric('A', 5, 1);
        $metricB    = new MethodMetric('B', 6, 0);
        $fileMetric = new FileMetric('/tmp/a/', 0, 20, [$metricA, $metricB], []);

        // expect metric B
        $result = FileMetricAnalyzer::getUncoveredMethodMetric($fileMetric);
        static::assertSame($metricB, $result);
    }

    public function testGetUncoveredMethodMetricShouldReturnNullForMethodsThatAreCovered(): void
    {
        $metricA    = new MethodMetric('A', 5, 2);
        $metricB    = new MethodMetric('B', 6, 3);
        $fileMetric = new FileMetric('/tmp/a/', 0, 20, [$metricA, $metricB], []);

        static::assertNull(FileMetricAnalyzer::getUncoveredMethodMetric($fileMetric));
    }
}
