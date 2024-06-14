<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Lib\IO;

use DigitalRevolution\CodeCoverageInspection\Lib\IO\InspectionConfigFactory;
use DOMDocument;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(\DigitalRevolution\CodeCoverageInspection\Lib\IO\InspectionConfigFactory::class)]
class InspectionConfigFactoryTest extends TestCase
{
    public function testFromDOMDocument(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <phpfci min-coverage="85">
                <custom-coverage>
                    <directory path="dir/ectory" min="60"/>
                    <file path="a/b/c" min="80"/>
                </custom-coverage>
            </phpfci>
        ';

        $dom = new DOMDocument();
        $dom->loadXML($xml);

        $config = InspectionConfigFactory::fromDOMDocument('/tmp/test', $dom);
        static::assertSame('/tmp/test/', $config->getBasePath());
        static::assertSame(85, $config->getMinimumCoverage());
        static::assertFalse($config->isUncoveredAllowed());

        $dir = $config->getPathInspection('dir/ectory/file');
        static::assertNotNull($dir);
        static::assertSame(60, $dir->getMinimumCoverage());
        static::assertSame('dir/ectory/', $dir->getPath());

        $file = $config->getPathInspection('a/b/c');
        static::assertNotNull($file);
        static::assertSame(80, $file->getMinimumCoverage());
        static::assertSame('a/b/c', $file->getPath());
    }

    public function testFromDOMDocumentWithUncoveredMethodsAllowed(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <phpfci min-coverage="85" allow-uncovered-methods="true"></phpfci>
        ';

        $dom = new DOMDocument();
        $dom->loadXML($xml);

        $config = InspectionConfigFactory::fromDOMDocument('/tmp/test', $dom);
        static::assertTrue($config->isUncoveredAllowed());
    }

    public function testFromDOMDocumentWithUncoveredMethodsForcedDisallowed(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <phpfci min-coverage="85" allow-uncovered-methods="false"></phpfci>
        ';

        $dom = new DOMDocument();
        $dom->loadXML($xml);

        $config = InspectionConfigFactory::fromDOMDocument('/tmp/test', $dom);
        static::assertFalse($config->isUncoveredAllowed());
    }

    public function testFromDOMDocumentWithIgnoreUncoveredMethods(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <phpfci min-coverage="85">
                <ignore-uncovered-methods>
                    <file path="a/b/c"/>
                </ignore-uncovered-methods>
            </phpfci>
        ';

        $dom = new DOMDocument();
        $dom->loadXML($xml);

        $config = InspectionConfigFactory::fromDOMDocument('/tmp/test', $dom);
        static::assertTrue($config->hasIgnoreUncoveredMethodFile('a/b/c'));
    }

    public function testFromDOMDocumentInvalidFormatThrowsException(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                <foobar></foobar>';

        $dom = new DOMDocument();
        $dom->loadXML($xml);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing `phpfci` in configuration file');
        InspectionConfigFactory::fromDOMDocument('/tmp/test', $dom);
    }
}
