<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Lib\IO;

use DigitalRevolution\CodeCoverageInspection\Lib\IO\MetricsFactory;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\MethodMetric;
use DOMDocument;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DigitalRevolution\CodeCoverageInspection\Lib\IO\MetricsFactory
 */
class MetricsFactoryTest extends TestCase
{
    /**
     * @covers ::getFilesMetrics
     * @covers ::getFileMetrics
     * @covers ::getMethodMetrics
     */
    public function testGetFilesMetrics(): void
    {
        $xml1 = '<?xml version="1.0" encoding="UTF-8"?>
    <coverage generated="1598702199">
        <project timestamp="1598702199">
            <file name="/API\\Example.php">
                <metrics loc="11" ncloc="11" classes="0"
                    methods="0" coveredmethods="0"
                    conditionals="0" coveredconditionals="0"
                    statements="50" coveredstatements="10"
                    elements="0" coveredelements="0"/>
            </file>
            <file name="/API\\Example2.php">
                <metrics loc="11" ncloc="11" classes="0"
                    methods="0" coveredmethods="0"
                    conditionals="0" coveredconditionals="0"
                    statements="50" coveredstatements="50"
                    elements="0" coveredelements="0"/>
            </file>
            <file name="/API\\Example3.php">
                <metrics loc="11" ncloc="11" classes="0"
                    methods="0" coveredmethods="0"
                    conditionals="0" coveredconditionals="0"
                    statements="50" coveredstatements="10"
                    elements="0" coveredelements="0"/>
            </file>
        </project>
    </coverage>';

        $dom1 = new DOMDocument();
        $dom1->loadXML($xml1);

        $xml2 = '<?xml version="1.0" encoding="UTF-8"?>
    <coverage generated="1598702199">
        <project timestamp="1598702199">
            <file name="/API\\Example.php">
                <metrics loc="11" ncloc="11" classes="0"
                    methods="0" coveredmethods="0"
                    conditionals="0" coveredconditionals="0"
                    statements="50" coveredstatements="25"
                    elements="0" coveredelements="0"/>
            </file>
            <file name="/API\\Example2.php">
                <metrics loc="11" ncloc="11" classes="0"
                    methods="0" coveredmethods="0"
                    conditionals="0" coveredconditionals="0"
                    statements="50" coveredstatements="5"
                    elements="0" coveredelements="0"/>
            </file>
        </project>
    </coverage>';

        $dom2 = new DOMDocument();
        $dom2->loadXML($xml2);

        $metrics = MetricsFactory::getFilesMetrics([$dom1, $dom2]);
        static::assertCount(3, $metrics);

        $metric = $metrics['/API/Example.php'];
        static::assertSame('/API/Example.php', $metric->getFilepath());
        static::assertSame([], $metric->getMethods());
        static::assertSame(50.0, $metric->getCoverage());

        $metric = $metrics['/API/Example2.php'];
        static::assertSame('/API/Example2.php', $metric->getFilepath());
        static::assertSame([], $metric->getMethods());
        static::assertSame(100.0, $metric->getCoverage());

        $metric = $metrics['/API/Example3.php'];
        static::assertSame('/API/Example3.php', $metric->getFilepath());
        static::assertSame([], $metric->getMethods());
        static::assertSame(20.0, $metric->getCoverage());
    }

    /**
     * @covers ::getFileMetrics
     * @covers ::getMethodMetrics
     */
    public function testGetMethodMetrics(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
    <coverage generated="1598702199">
        <project timestamp="1598702199">
            <file name="/API\\Example.php">
                <metrics loc="11" ncloc="11" classes="0"
                    methods="0" coveredmethods="0"
                    conditionals="0" coveredconditionals="0"
                    statements="50" coveredstatements="10"
                    elements="0" coveredelements="0"/>
                <line num="29" type="method" name="isFoobar" visibility="public" complexity="1" crap="1" count="1"/>
                <line num="31" type="stmt" count="1"/>
            </file>
        </project>
    </coverage>';

        $dom = new DOMDocument();
        $dom->loadXML($xml);

        $metrics = MetricsFactory::getFileMetrics($dom);
        static::assertCount(1, $metrics);

        $metric = reset($metrics);
        static::assertSame('/API/Example.php', $metric->getFilepath());
        static::assertEquals([new MethodMetric('isFoobar', 29, 1)], $metric->getMethods());
        static::assertSame(20.0, $metric->getCoverage());
    }

    /**
     * @covers ::getFilesMetrics
     * @covers ::getFileMetrics
     */
    public function testGetMetricsEmptyXmlShouldReturnEmptyArray(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
    <coverage generated="1598702199">
        <project timestamp="1598702199"></project>
    </coverage>';

        $dom = new DOMDocument();
        $dom->loadXML($xml);

        static::assertCount(0, MetricsFactory::getFilesMetrics([$dom]));
    }
}
