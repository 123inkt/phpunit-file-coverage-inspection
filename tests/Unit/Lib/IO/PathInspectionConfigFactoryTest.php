<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Lib\IO;

use DigitalRevolution\CodeCoverageInspection\Lib\IO\PathInspectionConfigFactory;
use DOMDocument;
use DOMException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @coversDefaultClass \DigitalRevolution\CodeCoverageInspection\Lib\IO\PathInspectionConfigFactory
 */
class PathInspectionConfigFactoryTest extends TestCase
{
    /**
     * @covers ::createFromNode
     * @throws DOMException
     */
    public function testCreateFromNodeInvalidNodeThrowsException(): void
    {
        $doc  = new DOMDocument();
        $node = $doc->createElement('foobar');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid node type: foobar');
        PathInspectionConfigFactory::createFromNode($node);
    }

    /**
     * @covers ::createFromNode
     * @throws DOMException
     */
    public function testCreateFromNodeForTypeFile(): void
    {
        $doc = new DOMDocument();

        $pathAttr        = $doc->createAttribute('path');
        $pathAttr->value = 'path';

        $minAttr        = $doc->createAttribute('min');
        $minAttr->value = '86';

        $node = $doc->createElement('file');
        $node->appendChild($pathAttr);
        $node->appendChild($minAttr);

        $config = PathInspectionConfigFactory::createFromNode($node);
        static::assertTrue($config->isFile());
        static::assertSame('path', $config->getPath());
        static::assertSame(86, $config->getMinimumCoverage());
    }

    /**
     * @covers ::createFromNode
     * @throws DOMException
     */
    public function testCreateFromNodeForTypeDirectory(): void
    {
        $doc = new DOMDocument();

        $pathAttr        = $doc->createAttribute('path');
        $pathAttr->value = '\\path';

        $minAttr        = $doc->createAttribute('min');
        $minAttr->value = '86';

        $node = $doc->createElement('directory');
        $node->appendChild($pathAttr);
        $node->appendChild($minAttr);

        $config = PathInspectionConfigFactory::createFromNode($node);
        static::assertTrue($config->isDirectory());
        static::assertSame('path/', $config->getPath());
        static::assertSame(86, $config->getMinimumCoverage());
    }
}
