<?php
declare(strict_types=1);

namespace DR\CodeCoverageInspection\Tests\Unit\Lib\Utility;

use DOMDocument;
use DR\CodeCoverageInspection\Lib\Utility\XMLUtil;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DR\CodeCoverageInspection\Lib\Utility\XMLUtil
 */
class XMLUtilTest extends TestCase
{
    /**
     * @covers ::getAttribute
     */
    public function testGetAttribute(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><phpcci min-coverage="85"></phpcci>';
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
        $xml = '<?xml version="1.0" encoding="UTF-8"?><phpcci>text</phpcci>';
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        static::assertNotNull($dom->firstChild);
        static::assertNotNull($dom->firstChild->firstChild);
        static::assertNull($dom->firstChild->firstChild->attributes);

        static::assertNull(XMLUtil::getAttribute($dom->firstChild->firstChild, 'unknown'));
    }
}
