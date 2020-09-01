<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Lib\IO;

use DigitalRevolution\CodeCoverageInspection\Lib\IO\InspectionConfigFactory;
use DOMDocument;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @coversDefaultClass \DigitalRevolution\CodeCoverageInspection\Lib\IO\InspectionConfigFactory
 */
class InspectionConfigFactoryTest extends TestCase
{
    /**
     * @covers ::fromDOMDocument
     * @covers ::getMinimumCoverage
     */
    public function testFromDOMDocument(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <phpcci min-coverage="85">
                <custom-coverage>
                    <file path="a/b/c" min="80"/>
                </custom-coverage>
            </phpcci>
        ';

        $dom = new DOMDocument();
        $dom->loadXML($xml);

        $config = InspectionConfigFactory::fromDOMDocument('/tmp/test', $dom);
        static::assertSame('/tmp/test', $config->getBasePath());
        static::assertSame(85, $config->getMinimumCoverage());

        $file = $config->getFileInspection('a/b/c');
        static::assertNotNull($file);
        static::assertSame(80, $file->getMinimumCoverage());
        static::assertSame('a/b/c', $file->getPath());
    }

    /**
     * @covers ::fromDOMDocument
     * @covers ::getMinimumCoverage
     */
    public function testFromDOMDocumentInvalidFormatThrowsException(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                <foobar></foobar>';

        $dom = new DOMDocument();
        $dom->loadXML($xml);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing `phpcci` in configuration file');
        InspectionConfigFactory::fromDOMDocument('/tmp/test', $dom);
    }
}
