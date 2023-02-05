<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Model\Config;

use DigitalRevolution\CodeCoverageInspection\Model\Config\IgnoreUncoveredMethodFile;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DigitalRevolution\CodeCoverageInspection\Model\Config\IgnoreUncoveredMethodFile
 * @covers ::__construct
 */
class IgnoreUncoveredMethodFileTest extends TestCase
{
    /**
     * @covers ::getFilepath
     */
    public function testGetFilepath(): void
    {
        $file = new IgnoreUncoveredMethodFile('/test/file\\path.php');
        static::assertSame('/test/file/path.php', $file->getFilepath());
    }
}
