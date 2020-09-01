<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Lib\IO;

use DigitalRevolution\CodeCoverageInspection\Lib\IO\MetricsFactory;
use DOMDocument;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DigitalRevolution\CodeCoverageInspection\Lib\IO\MetricsFactory
 */
class MetricsFactoryTest extends TestCase
{
    /**
     * @covers ::getMetrics
     */
    public function testGetMetrics(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
    <coverage generated="1598702199">
        <project timestamp="1598702199">
            <file name="/API/Example.php">
                <metrics loc="11" ncloc="11" classes="0"
                    methods="0" coveredmethods="0"
                    conditionals="0" coveredconditionals="0"
                    statements="50" coveredstatements="10"
                    elements="0" coveredelements="0"/>
            </file>
        </project>
    </coverage>';

        $dom = new DOMDocument();
        $dom->loadXML($xml);

        $metrics = MetricsFactory::getMetrics($dom);
        static::assertCount(1, $metrics);

        $metric = reset($metrics);
        static::assertSame('/API/Example.php', $metric->getFilepath());
        static::assertSame(20.0, $metric->getCoverage());
    }

    /**
     * @covers ::getMetrics
     */
    public function testGetMetricsEmptyXmlShouldReturnEmptyArray(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
    <coverage generated="1598702199">
        <project timestamp="1598702199"></project>
    </coverage>';

        $dom = new DOMDocument();
        $dom->loadXML($xml);

        static::assertCount(0, MetricsFactory::getMetrics($dom));
    }
}
