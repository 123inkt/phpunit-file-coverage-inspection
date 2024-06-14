<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Renderer;

use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;
use DigitalRevolution\CodeCoverageInspection\Renderer\TextRenderer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TextRenderer::class)]
class TextRendererTest extends TestCase
{
    public function testRenderGlobalCoverageTooLow(): void
    {
        $config   = new InspectionConfig('', 80);
        $failureA = new Failure(new FileMetric('/short/file/path.php', 0, 48.3, [], []), 80, Failure::GLOBAL_COVERAGE_TOO_LOW, 5);
        $failureB = new Failure(new FileMetric('/a/medium/file/path.php', 0, 42.5, [], []), 80, Failure::CUSTOM_COVERAGE_TOO_LOW, 10);
        $failureC = new Failure(new FileMetric('/a/very/very/long/file/path.php', 0, 67.3, [], []), 80, Failure::CUSTOM_COVERAGE_TOO_LOW, 200);

        $renderer = new TextRenderer();
        $result   = $renderer->render($config, [$failureA, $failureB, $failureC]);

        $expected = '/short/file/path.php:5                       ';
        $expected .= 'Project per file coverage is configured at 80%. Current coverage is at 48.3%. Improve coverage for this class.' . PHP_EOL;
        $expected .= '/a/medium/file/path.php:10                   ';
        $expected .= 'Custom file coverage is configured at 80%. Current coverage is at 42.5%. Improve coverage for this class.' . PHP_EOL;
        $expected .= '/a/very/very/long/file/path.php:200          ';
        $expected .= 'Custom file coverage is configured at 80%. Current coverage is at 67.3%. Improve coverage for this class.' . PHP_EOL;

        static::assertSame($expected, $result);
    }
}
