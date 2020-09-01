<?php
declare(strict_types=1);

namespace DR\CodeCoverageInspection\Tests\Unit\Lib\IO;

use DR\CodeCoverageInspection\Lib\IO\DOMDocumentFactory;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use SplFileInfo;

/**
 * @coversDefaultClass \DR\CodeCoverageInspection\Lib\IO\DOMDocumentFactory
 */
class DOMDocumentFactoryTest extends TestCase
{
    /** @var resource */
    private $file;

    /** @var SplFileInfo */
    private $fileInfo;

    /** @var string */
    private $schemaPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->file       = tmpfile();
        $this->fileInfo   = new SplFileInfo(stream_get_meta_data($this->file)['uri']);
        $this->schemaPath = dirname(__DIR__, 4) . '/resources/phpcci.xsd';
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
            <phpcci min-coverage="85">
                <custom-coverage>
                    <file path="a/b/c" min="80"/>
                </custom-coverage>
            </phpcci>
        ';
        fwrite($this->file, $xml);
        $dom = DOMDocumentFactory::getValidatedDOMDocument($this->fileInfo, $this->schemaPath);
        static::assertSame('1.0', $dom->version);
    }

    /**
     * @covers ::getDOMDocument
     * @covers ::getValidatedDOMDocument
     */
    public function testGetDOMDocumentWithoutCustomCoverageShouldPass(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <phpcci min-coverage="85">
            </phpcci>
        ';
        fwrite($this->file, $xml);
        $dom = DOMDocumentFactory::getValidatedDOMDocument($this->fileInfo, $this->schemaPath);
        static::assertSame('1.0', $dom->version);
    }

    /**
     * @covers ::getDOMDocument
     * @covers ::getValidatedDOMDocument
     */
    public function testGetDOMDocumentWithInvalidCoverageShouldFail(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <phpcci min-coverage="200">
            </phpcci>
        ';
        fwrite($this->file, $xml);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("is not a valid value of the atomic");
        DOMDocumentFactory::getValidatedDOMDocument($this->fileInfo, $this->schemaPath);
    }

    /**
     * @covers ::getDOMDocument
     * @covers ::getValidatedDOMDocument
     */
    public function testGetDOMDocumentWithInvalidCoverage(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <phpcci min-coverage="200">
                <custom-coverage>
                </custom-coverage>
            </phpcci>
        ';
        fwrite($this->file, $xml);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Missing child element(s)");
        DOMDocumentFactory::getValidatedDOMDocument($this->fileInfo, $this->schemaPath);
    }
}
