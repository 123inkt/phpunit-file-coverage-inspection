<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Model\Config;

use DigitalRevolution\CodeCoverageInspection\Model\Config\PathInspectionConfig;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DigitalRevolution\CodeCoverageInspection\Model\Config\PathInspectionConfig
 */
class FileInspectionConfigTest extends TestCase
{
    /**
     * @covers ::<public>
     */
    public function testAccessors(): void
    {
        $config = new PathInspectionConfig(PathInspectionConfig::TYPE_FILE, 'path', 86);
        static::assertTrue($config->isFile());
        static::assertFalse($config->isDirectory());
        static::assertSame('path', $config->getPath());
        static::assertSame(86, $config->getMinimumCoverage());
    }
}
