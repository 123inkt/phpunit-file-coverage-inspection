<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Lib\Utility;

use DigitalRevolution\CodeCoverageInspection\Lib\Utility\XMLUtil;
use DOMDocument;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DigitalRevolution\CodeCoverageInspection\Lib\Utility\XMLUtil
 */
class XMLUtilTest extends TestCase
{
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
