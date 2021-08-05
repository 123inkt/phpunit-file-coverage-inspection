<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Renderer;

use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;
use DigitalRevolution\CodeCoverageInspection\Renderer\GitlabErrorRenderer;
use JsonException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DigitalRevolution\CodeCoverageInspection\Renderer\GitlabErrorRenderer
 */
class GitlabErrorRendererTest extends TestCase
{
    /**
     * @covers ::render
     * @throws JsonException
     */
    public function testRender(): void
    {
        $config  = new InspectionConfig('', 80);
        $metric  = new FileMetric('/foo/bar/file.php', 48.3, []);
        $failure = new Failure($metric, 80, Failure::GLOBAL_COVERAGE_TOO_LOW);

        $checkStyle = new GitlabErrorRenderer();
        $result     = $checkStyle->render($config, [$failure]);

        static::assertSame(
            [
                [
                    'description' => 'Project per file coverage is configured at 80%. Current coverage is at 48.3%. Improve coverage for this class.',
                    'fingerprint' => 'b798f95fb1a0d73279de8818d0403284c8fe97d7eae224631b741784d5c4b9d1',
                    'severity'    => 'major',
                    'location'    => [
                        'path'  => '/foo/bar/file.php',
                        'lines' => [
                            'begin' => 1
                        ]
                    ]
                ]
            ],
            json_decode($result, true, 512, JSON_THROW_ON_ERROR)
        );
    }
}
