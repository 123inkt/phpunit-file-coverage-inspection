<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Lib\IO;

use DigitalRevolution\CodeCoverageInspection\Lib\IO\DOMDocumentFactory;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use SplFileInfo;

/**
 * @coversDefaultClass \DigitalRevolution\CodeCoverageInspection\Lib\IO\DOMDocumentFactory
 */
class DOMDocumentFactoryTest extends TestCase
{
    /** @var resource */
    private $file;

    private SplFileInfo $fileInfo;
    private string $schemaPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->file       = tmpfile();
        $this->fileInfo   = new SplFileInfo(stream_get_meta_data($this->file)['uri']);
        $this->schemaPath = dirname(__DIR__, 4) . '/resources/phpfci.xsd';
    }

    /**
     * @covers ::getDOMDocument
     */
    public function testGetDOMDocumentMissingFileShouldFail(): void
    {
        $file = new SplFileInfo('/a/b/c');

        $this->expectException(RuntimeException::class);
        DOMDocumentFactory::getDOMDocument($file);
    }

    /**
     * @covers ::getDOMDocument
     * @covers ::getValidatedDOMDocument
     */
    public function testGetDOMDocumentShouldPass(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <phpfci min-coverage="85">
                <custom-coverage>
                    <file path="a/b/c" min="80"/>
                </custom-coverage>
            </phpfci>
        ';
        fwrite($this->file, $xml);
        $dom = DOMDocumentFactory::getValidatedDOMDocument($this->fileInfo, $this->schemaPath);
        static::assertSame('1.0', $dom->xmlVersion);
    }

    /**
     * @covers ::getDOMDocument
     * @covers ::getValidatedDOMDocument
     */
    public function testGetDOMDocumentWithoutCustomCoverageShouldPass(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <phpfci min-coverage="85">
            </phpfci>
        ';
        fwrite($this->file, $xml);
        $dom = DOMDocumentFactory::getValidatedDOMDocument($this->fileInfo, $this->schemaPath);
        static::assertSame('1.0', $dom->xmlVersion);
    }

    /**
     * @covers ::getDOMDocument
     * @covers ::getValidatedDOMDocument
     */
    public function testGetDOMDocumentWithInvalidCoverageShouldFail(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <phpfci min-coverage="200">
            </phpfci>
        ';
        fwrite($this->file, $xml);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Xml doesn't have the correct format");
        DOMDocumentFactory::getValidatedDOMDocument($this->fileInfo, $this->schemaPath);
    }

    /**
     * @covers ::getDOMDocument
     * @covers ::getValidatedDOMDocument
     */
    public function testGetDOMDocumentWithInvalidCoverage(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <phpfci min-coverage="200">
                <custom-coverage>
                </custom-coverage>
            </phpfci>
        ';
        fwrite($this->file, $xml);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Missing child element(s). Expected is one of ( directory, file )");
        DOMDocumentFactory::getValidatedDOMDocument($this->fileInfo, $this->schemaPath);
    }
}
