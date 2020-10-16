<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Renderer;

use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;
use DigitalRevolution\CodeCoverageInspection\Renderer\CheckStyleRenderer;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @coversDefaultClass \DigitalRevolution\CodeCoverageInspection\Renderer\CheckStyleRenderer
 */
class CheckStyleRendererTest extends TestCase
{
    /**
     * @covers ::render
     * @covers ::formatReason
     */
    public function testRenderGlobalCoverageTooLow(): void
    {
        $config  = new InspectionConfig('', 80);
        $metric  = new FileMetric('/foo/bar/file.php', 48.3, []);
        $failure = new Failure($metric, 80, Failure::GLOBAL_COVERAGE_TOO_LOW);

        $checkStyle = new CheckStyleRenderer();
        $result     = $checkStyle->render($config, [$failure]);

        static::assertSame(
            "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
            "<checkstyle version=\"3.5.5\">\n" .
            " <file name=\"/foo/bar/file.php\">\n" .
            "  <error line=\"1\" column=\"0\" severity=\"error\" message=\"Project per file coverage is configured at 80%. Current coverage is" .
            " at 48.3%. Improve coverage for this class.\" source=\"phpunit-file-coverage-inspection\"/>\n" .
            " </file>\n" .
            "</checkstyle>\n",
            $result
        );
    }

    /**
     * @covers ::render
     * @covers ::formatReason
     */
    public function testRenderFileCoverageTooLow(): void
    {
        $config  = new InspectionConfig('', 80);
        $metric  = new FileMetric('/foo/bar/file.php', 48.3, []);
        $failure = new Failure($metric, 60, Failure::CUSTOM_COVERAGE_TOO_LOW);

        $checkStyle = new CheckStyleRenderer();
        $result     = $checkStyle->render($config, [$failure]);

        static::assertSame(
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
            '<checkstyle version="3.5.5">' . "\n" .
            ' <file name="/foo/bar/file.php">' . "\n" .
            '  <error line="1" column="0" severity="error" message="Custom file coverage is configured at 60%. Current coverage is' .
            ' at 48.3%. Improve coverage for this class." source="phpunit-file-coverage-inspection"/>' . "\n" .
            ' </file>' . "\n" .
            '</checkstyle>' . "\n",
            $result
        );
    }

    /**
     * @covers ::render
     * @covers ::formatReason
     */
    public function testRenderMissingMethodCoverage(): void
    {
        $config  = new InspectionConfig('', 80);
        $metric  = new FileMetric('/foo/bar/file.php', 85.3, []);
        $failure = new Failure($metric, 60, Failure::MISSING_METHOD_COVERAGE, 20);

        $checkStyle = new CheckStyleRenderer();
        $result     = $checkStyle->render($config, [$failure]);

        static::assertSame(
            "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
            "<checkstyle version=\"3.5.5\">\n" .
            " <file name=\"/foo/bar/file.php\">\n" .
            "  <error line=\"20\" column=\"0\" severity=\"error\" message=\"File coverage is above 80%, but method has no coverage at all.\"" .
            " source=\"phpunit-file-coverage-inspection\"/>\n" .
            " </file>\n" .
            "</checkstyle>\n",
            $result
        );
    }

    /**
     * @covers ::render
     * @covers ::formatReason
     */
    public function testRenderUnnecessaryFileCoverage(): void
    {
        $config  = new InspectionConfig('', 80);
        $metric  = new FileMetric('/foo/bar/file.php', 85.3, []);
        $failure = new Failure($metric, 60, Failure::UNNECESSARY_CUSTOM_COVERAGE);

        $checkStyle = new CheckStyleRenderer();
        $result     = $checkStyle->render($config, [$failure]);

        static::assertSame(
            "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
            "<checkstyle version=\"3.5.5\">\n" .
            " <file name=\"/foo/bar/file.php\">\n" .
            "  <error line=\"1\" column=\"0\" severity=\"error\" message=\"A custom file coverage is configured at 60%, but the current file" .
            " coverage 85.3% exceeds the project coverage 80%. Remove `/foo/bar/file.php` from phpfci.xml custom-coverage rules.\"" .
            " source=\"phpunit-file-coverage-inspection\"/>\n" .
            " </file>\n" .
            "</checkstyle>\n",
            $result
        );
    }

    /**
     * @covers ::render
     * @covers ::formatReason
     */
    public function testRenderInvalidReasonThrowsException(): void
    {
        $config     = new InspectionConfig('', 80);
        $metric     = new FileMetric('/foo/bar/file.php', 85.3, []);
        $failure    = new Failure($metric, 60, -4);
        $checkStyle = new CheckStyleRenderer();

        $this->expectException(RuntimeException::class);
        $checkStyle->render($config, [$failure]);
    }
}
