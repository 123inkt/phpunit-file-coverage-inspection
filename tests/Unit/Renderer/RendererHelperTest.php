<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Renderer;

use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\MethodMetric;
use DigitalRevolution\CodeCoverageInspection\Renderer\RendererHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(RendererHelper::class)]
class RendererHelperTest extends TestCase
{
    private InspectionConfig $config;

    public function testRenderReasonGlobalCoverageTooLow(): void
    {
        $metric  = new FileMetric('foobar', 0, 80, [], []);
        $failure = new Failure($metric, 20, Failure::GLOBAL_COVERAGE_TOO_LOW, 5);

        $message = RendererHelper::renderReason($this->config, $failure);
        static::assertSame('Project per file coverage is configured at 20%. Current coverage is at 80%. Improve coverage for this class.', $message);
    }

    public function testRenderReasonCustomCoverageTooLow(): void
    {
        $metric  = new FileMetric('foobar', 0, 70, [], []);
        $failure = new Failure($metric, 30, Failure::CUSTOM_COVERAGE_TOO_LOW, 5);

        $message = RendererHelper::renderReason($this->config, $failure);
        static::assertSame('Custom file coverage is configured at 30%. Current coverage is at 70%. Improve coverage for this class.', $message);
    }

    public function testRenderReasonCustomCoverageTooHigh(): void
    {
        $metric  = new FileMetric('foobar', 0, 50, [], []);
        $failure = new Failure($metric, 70, Failure::CUSTOM_COVERAGE_TOO_HIGH, 5);

        $message = RendererHelper::renderReason($this->config, $failure);
        static::assertSame(
            'Custom file coverage is configured at 70%. Current coverage is at 50%. Edit the phpfci baseline for this class.',
            $message
        );
    }

    public function testRenderReasonMissingMethodCoverage(): void
    {
        $metric  = new FileMetric('foobar', 0, 70, [new MethodMetric('coveredMethod', 100, 80), new MethodMetric('uncoveredMethod', 105, 0)], []);
        $failure = new Failure($metric, 30, Failure::MISSING_METHOD_COVERAGE, 5);

        $message = RendererHelper::renderReason($this->config, $failure);
        static::assertSame('File coverage is above 80%, but method(s) `uncoveredMethod` has/have no coverage at all.', $message);
    }

    public function testRenderReasonUnnecessaryCustomCoverage(): void
    {
        $metric  = new FileMetric('foobar', 0, 70, [], []);
        $failure = new Failure($metric, 30, Failure::UNNECESSARY_CUSTOM_COVERAGE, 5);

        $message = RendererHelper::renderReason($this->config, $failure);
        static::assertSame(
            'A custom file coverage is configured at 30%, but the current file coverage 70% ' .
            'exceeds the project coverage 80%. Remove `foobar` from phpfci.xml custom-coverage rules.',
            $message
        );
    }

    public function testRenderReasonShouldThrowExceptionWhenInvalid(): void
    {
        $metric  = new FileMetric('foobar', 0, 70, [], []);
        $failure = new Failure($metric, 30, 9999, 5);

        $this->expectException(RuntimeException::class);
        RendererHelper::renderReason($this->config, $failure);
    }

    protected function setUp(): void
    {
        $this->config = new InspectionConfig('base-path', 80);
    }
}
