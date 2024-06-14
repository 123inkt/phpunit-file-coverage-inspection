<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Model\Config;

use DigitalRevolution\CodeCoverageInspection\Model\Config\IgnoreUncoveredMethodFile;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\DigitalRevolution\CodeCoverageInspection\Model\Config\IgnoreUncoveredMethodFile::class)]
class IgnoreUncoveredMethodFileTest extends TestCase
{
    public function testGetFilepath(): void
    {
        $file = new IgnoreUncoveredMethodFile('/test/file\\path.php');
        static::assertSame('/test/file/path.php', $file->getFilepath());
    }
}
