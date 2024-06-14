<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Lib\IO;

use DigitalRevolution\CodeCoverageInspection\Lib\IO\MetricsFactory;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\MethodMetric;
use DOMDocument;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(\DigitalRevolution\CodeCoverageInspection\Lib\IO\MetricsFactory::class)]
class MetricsFactoryTest extends TestCase
{
    #[DataProvider('multiFilesDataProvider')]
    public function testGetFilesMetrics(string $file1Data, string $file2Data, int $methods, float $coverage, array $coveredStatements): void
    {
        $xml1 = '<?xml version="1.0" encoding="UTF-8"?>
    <coverage generated="1598702199">
        <project timestamp="1598702199">
' . $file1Data . '
        </project>
    </coverage>';

        $dom1 = new DOMDocument();
        $dom1->loadXML($xml1);

        $xml2 = '<?xml version="1.0" encoding="UTF-8"?>
    <coverage generated="1598702199">
        <project timestamp="1598702199">
           ' . $file2Data . '
        </project>
    </coverage>';

        $dom2 = new DOMDocument();
        $dom2->loadXML($xml2);

        $metrics = MetricsFactory::getFilesMetrics([$dom1, $dom2]);
        static::assertCount(1, $metrics);

        $metric = $metrics['utTestFile.php'];
        static::assertSame('utTestFile.php', $metric->getFilepath());
        static::assertCount($methods, $metric->getMethods());
        static::assertSame($coverage, $metric->getCoverage());
        static::assertSame($coveredStatements, $metric->getCoveredStatements());
    }

    public function testGetMethodMetrics(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
    <coverage generated="1598702199">
        <project timestamp="1598702199">
            <file name="/API\\Example.php">
                <metrics loc="11" ncloc="11" classes="0"
                    methods="0" coveredmethods="0"
                    conditionals="0" coveredconditionals="0"
                    statements="5" coveredstatements="1"
                    elements="0" coveredelements="0"/>
                <line num="29" type="method" name="isFoobar" visibility="public" complexity="1" crap="1" count="1"/>
                <line num="31" type="stmt" count="0"/>
                <line num="32" type="stmt" count="0"/>
                <line num="33" type="stmt" count="1"/>
                <line num="34" type="stmt" count="0"/>
                <line num="35" type="stmt" count="0"/>
                <line num="45" type="method" name="isEmpty" visibility="public" complexity="0" crap="0" count="0"/>
            </file>
        </project>
    </coverage>';

        $dom = new DOMDocument();
        $dom->loadXML($xml);

        $metrics = MetricsFactory::getFileMetrics($dom);
        static::assertCount(1, $metrics);

        $metric = reset($metrics);
        static::assertSame('/API/Example.php', $metric->getFilepath());
        static::assertEquals(['isFoobar' => new MethodMetric('isFoobar', 29, 1)], $metric->getMethods());
        static::assertSame(20.0, $metric->getCoverage());
    }

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

    public static function multiFilesDataProvider(): array
    {
        return [
            'second coverage is used' => [
                '<file name="utTestFile.php">
                <line num="1" type="method" name="methodName" complexity="1" count="2"/>
                <line num="2" type="stmt" count="2"/>
                <line num="3" type="stmt" count="0"/>
                <line num="4" type="stmt" count="0"/>
                <line num="5" type="stmt" count="0"/>
                <metrics loc="11" ncloc="11" statements="4" coveredstatements="1"/>
            </file>',
                '<file name="utTestFile.php">
                <line num="1" type="method" name="methodName" complexity="1" count="1"/>
                <line num="2" type="stmt" count="1"/>
                <line num="3" type="stmt" count="0"/>
                <line num="4" type="stmt" count="1"/>
                <line num="5" type="stmt" count="0"/>
                <metrics loc="11" ncloc="11" statements="4" coveredstatements="2"/>
            </file>',
                1,
                50.0,
                [2, 4]
            ],
            'second count is higher'  => [
                '<file name="utTestFile.php">
                <line num="1" type="method" name="methodName" complexity="1" count="1"/>
                <line num="2" type="stmt" count="1"/>
                <line num="3" type="stmt" count="1"/>
                <line num="4" type="stmt" count="1"/>
                <line num="5" type="stmt" count="1"/>
                <metrics loc="11" ncloc="11" statements="4" coveredstatements="1"/>
            </file>',
                '<file name="utTestFile.php">
                <line num="1" type="method" name="methodName" complexity="1" count="2"/>
                <line num="2" type="stmt" count="1"/>
                <line num="3" type="stmt" count="0"/>
                <line num="4" type="stmt" count="1"/>
                <line num="5" type="stmt" count="0"/>
                <metrics loc="11" ncloc="11" statements="4" coveredstatements="2"/>
            </file>',
                1,
                100.0,
                [2, 3, 4, 5]
            ],
            'no second coverage'      => [
                '<file name="utTestFile.php">
                <metrics loc="11" ncloc="11" statements="0" coveredstatements="0"/>
            </file>',
                '',
                0,
                100.0,
                []
            ],
            'first 100 %'             => [
                '<file name="utTestFile.php">
                <metrics loc="11" ncloc="11" statements="1" coveredstatements="1"/>
                <line num="1" type="method" name="methodName" complexity="1" count="1"/>
                <line num="2" type="stmt" count="1"/>
            </file>',
                ' <file name="utTestFile.php">
                <metrics loc="11" ncloc="11" statements="0" coveredstatements="0"/>
                <line num="1" type="method" name="methodName" complexity="1" count="1"/>
            </file>',
                1,
                100.0,
                [2]
            ],
            'second 100%'             => [
                '<file name="utTestFile.php">
                <metrics loc="11" ncloc="11" statements="2" coveredstatements="0"/>
                <line num="1" type="method" name="methodName" complexity="1" count="1"/>
                <line num="2" type="stmt" count="0"/>
                <line num="3" type="stmt" count="0"/>
            </file>',
                '<file name="utTestFile.php">
                <metrics loc="11" ncloc="11" statements="2" coveredstatements="2"/>
                <line num="1" type="method" name="methodName" complexity="1" count="1"/>
                <line num="2" type="stmt" count="1"/>
                <line num="3" type="stmt" count="1"/>
            </file>',
                1,
                100.0,
                [2, 3]
            ]
        ];
    }
}
