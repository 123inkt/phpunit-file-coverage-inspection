<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Lib\IO;

use DigitalRevolution\CodeCoverageInspection\Lib\IO\IgnoreUncoveredMethodFileFactory;
use DOMDocument;
use DOMException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @coversDefaultClass \DigitalRevolution\CodeCoverageInspection\Lib\IO\IgnoreUncoveredMethodFileFactory
 */
class IgnoreUncoveredMethodFileFactoryTest extends TestCase
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
        IgnoreUncoveredMethodFileFactory::createFromNode($node);
    }

    /**
     * @covers ::createFromNode
     * @throws DOMException
     */
    public function testCreateFromNode(): void
    {
        $doc = new DOMDocument();

        $pathAttr        = $doc->createAttribute('path');
        $pathAttr->value = 'path/to/file';

        $node = $doc->createElement('file');
        $node->appendChild($pathAttr);

        $config = IgnoreUncoveredMethodFileFactory::createFromNode($node);
        static::assertSame('path/to/file', $config->getFilepath());
    }
}
