<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Lib\Utility;

use ArrayIterator;
use DigitalRevolution\CodeCoverageInspection\Lib\Utility\XMLUtil;
use DOMDocument;
use DOMException;
use DOMXPath;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DigitalRevolution\CodeCoverageInspection\Lib\Utility\XMLUtil
 */
class XMLUtilTest extends TestCase
{
    /**
     * @covers ::query
     */
    public function testQueryInvalidPath(): void
    {
        $xpath = $this->createMock(DOMXPath::class);
        $xpath->expects(self::once())->method('query')->with('foobar')->willReturn(false);

        static::assertSame([], XMLUtil::query($xpath, 'foobar'));
    }

    /**
     * @covers ::query
     * @throws DOMException
     */
    public function testQuerySuccessful(): void
    {
        $node = (new DOMDocument())->createElement('el');

        $xpath = $this->createMock(DOMXPath::class);
        $xpath->expects(self::once())->method('query')->with('foobar')->willReturn(new ArrayIterator([$node]));

        static::assertSame([$node], XMLUtil::query($xpath, 'foobar'));
    }

    /**
     * @covers ::getAttribute
     */
    public function testGetAttribute(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><phpfci min-coverage="85"></phpfci>';
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        static::assertNotNull($dom->firstChild);

        // assert that existing valid is found
        static::assertSame('85', XMLUtil::getAttribute($dom->firstChild, 'min-coverage'));

        // assert that non-existing returns null
        static::assertNull(XMLUtil::getAttribute($dom->firstChild, 'unknown'));
    }

    /**
     * @covers ::getAttribute
     */
    public function testGetAttributeNodeWithoutAttributesShouldReturnNull(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><phpfci>text</phpfci>';
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        static::assertNotNull($dom->firstChild);
        static::assertNotNull($dom->firstChild->firstChild);
        static::assertNull($dom->firstChild->firstChild->attributes);

        static::assertNull(XMLUtil::getAttribute($dom->firstChild->firstChild, 'unknown'));
    }
}
