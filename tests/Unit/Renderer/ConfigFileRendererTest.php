<?php
declare(strict_types=1);

namespace DR\CodeCoverageInspection\Tests\Unit\Renderer;

use DR\CodeCoverageInspection\Model\Config\InspectionConfig;
use DR\CodeCoverageInspection\Model\Metric\Failure;
use DR\CodeCoverageInspection\Model\Metric\Metric;
use DR\CodeCoverageInspection\Renderer\ConfigFileRenderer;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DR\CodeCoverageInspection\Renderer\ConfigFileRenderer
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
            '<phpcci xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ' .
            'xsi:noNamespaceSchemaLocation="vendor/digitalrevolution/phpunit-coverage-inspection/resources/phpcci.xsd" min-coverage="100">' . "\n" .
            '    <custom-coverage>' . "\n" .
            '        <file path="bar/file.php" min="48"/>' . "\n" .
            '    </custom-coverage>' . "\n" .
            '</phpcci>' . "\n",
            $result
        );
    }
}
