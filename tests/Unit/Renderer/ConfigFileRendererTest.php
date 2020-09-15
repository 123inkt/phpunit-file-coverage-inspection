<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Renderer;

use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Metric;
use DigitalRevolution\CodeCoverageInspection\Renderer\ConfigFileRenderer;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DigitalRevolution\CodeCoverageInspection\Renderer\ConfigFileRenderer
 */
class ConfigFileRendererTest extends TestCase
{
    /**
     * @covers ::render
     */
    public function testWrite(): void
    {
        $config  = new InspectionConfig('/foo/', 100);
        $metric  = new Metric('/foo/bar/file.php', 48.3);
        $failure = new Failure($metric, 60, Failure::GLOBAL_COVERAGE_TOO_LOW);

        $checkStyle = new ConfigFileRenderer();
        $result     = $checkStyle->render([$failure], $config);

        static::assertSame(
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
            '<phpfci xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ' .
            'xsi:noNamespaceSchemaLocation="vendor/digitalrevolution/phpunit-file-coverage-inspection/resources/phpfci.xsd" min-coverage="100">' . "\n" .
            '    <custom-coverage>' . "\n" .
            '        <file path="bar/file.php" min="48"/>' . "\n" .
            '    </custom-coverage>' . "\n" .
            '</phpfci>' . "\n",
            $result
        );
    }
}
