<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Renderer;

use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\MethodMetric;
use DigitalRevolution\CodeCoverageInspection\Renderer\RendererHelper;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @coversDefaultClass \DigitalRevolution\CodeCoverageInspection\Renderer\RendererHelper
 */
class RendererHelperTest extends TestCase
{
    private InspectionConfig $config;

    protected function setUp(): void
    {
        $this->config = new InspectionConfig('base-path', 80);
    }

    /**
     * @covers ::renderReason
     */
    public function testRenderReasonGlobalCoverageTooLow(): void
    {
        $metric  = new FileMetric('foobar', 80, []);
        $failure = new Failure($metric, 20, Failure::GLOBAL_COVERAGE_TOO_LOW, 5);

        $message = RendererHelper::renderReason($this->config, $failure);
        static::assertSame('Project per file coverage is configured at 20%. Current coverage is at 80%. Improve coverage for this class.', $message);
    }

    /**
     * @covers ::renderReason
     */
    public function testRenderReasonCustomCoverageTooLow(): void
    {
        $metric  = new FileMetric('foobar', 70, []);
        $failure = new Failure($metric, 30, Failure::CUSTOM_COVERAGE_TOO_LOW, 5);

        $message = RendererHelper::renderReason($this->config, $failure);
        static::assertSame('Custom file coverage is configured at 30%. Current coverage is at 70%. Improve coverage for this class.', $message);
    }

    /**
     * @covers ::renderReason
     */
    public function testRenderReasonMissingMethodCoverage(): void
    {
        $metric  = new FileMetric('foobar', 70, [new MethodMetric('myMethod', 100, 80)]);
        $failure = new Failure($metric, 30, Failure::MISSING_METHOD_COVERAGE, 5);

        $message = RendererHelper::renderReason($this->config, $failure);
        static::assertSame('File coverage is above 80%, but method(s) `myMethod` has/have no coverage at all.', $message);
    }

    /**
     * @covers ::renderReason
     */
    public function testRenderReasonUnnecessaryCustomCoverage(): void
    {
        $metric  = new FileMetric('foobar', 70, []);
        $failure = new Failure($metric, 30, Failure::UNNECESSARY_CUSTOM_COVERAGE, 5);

        $message = RendererHelper::renderReason($this->config, $failure);
        static::assertSame(
            'A custom file coverage is configured at 30%, but the current file coverage 70% ' .
            'exceeds the project coverage 80%. Remove `foobar` from phpfci.xml custom-coverage rules.',
            $message
        );
    }

    /**
     * @covers ::renderReason
     */
    public function testRenderReasonShouldThrowExceptionWhenInvalid(): void
    {
        $metric  = new FileMetric('foobar', 70, []);
        $failure = new Failure($metric, 30, 9999, 5);

        $this->expectException(RuntimeException::class);
        RendererHelper::renderReason($this->config, $failure);
    }
}
