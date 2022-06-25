<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Model\Config;

use DigitalRevolution\CodeCoverageInspection\Model\Config\PathInspectionConfig;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DigitalRevolution\CodeCoverageInspection\Model\Config\PathInspectionConfig
 */
class PathInspectionConfigTest extends TestCase
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

    /**
     * @covers ::__construct
     * @covers ::getPath
     */
    public function testGetPathNormalizeSlashes(): void
    {
        $config = new PathInspectionConfig(PathInspectionConfig::TYPE_FILE, '\\path\\to\\file', 86);
        static::assertSame('/path/to/file', $config->getPath());
    }

    /**
     * @covers ::__construct
     * @covers ::getPath
     */
    public function testGetPathNormalizeDirectory(): void
    {
        $config = new PathInspectionConfig(PathInspectionConfig::TYPE_DIR, '\\path\\to/directory', 86);
        static::assertSame('path/to/directory/', $config->getPath());
    }

    /**
     * @covers ::compare
     */
    public function testCompareFileAndDirectory(): void
    {
        $configA = new PathInspectionConfig(PathInspectionConfig::TYPE_FILE, 'foobar', 40);
        $configB = new PathInspectionConfig(PathInspectionConfig::TYPE_DIR, 'foobar', 50);

        // A should be stronger than B
        static::assertSame(1, $configA->compare($configB));
        static::assertSame(-1, $configB->compare($configA));
    }

    /**
     * @covers ::compare
     */
    public function testCompareFileAndFile(): void
    {
        $configA = new PathInspectionConfig(PathInspectionConfig::TYPE_FILE, 'foobar', 40);
        $configB = new PathInspectionConfig(PathInspectionConfig::TYPE_FILE, 'foobar', 50);

        // A should be equal to B
        static::assertSame(0, $configA->compare($configB));
        static::assertSame(0, $configB->compare($configA));
    }

    /**
     * @covers ::compare
     */
    public function testCompareDirectoryAndDirectory(): void
    {
        $configA = new PathInspectionConfig(PathInspectionConfig::TYPE_DIR, 'a/very/long/directory/', 40);
        $configB = new PathInspectionConfig(PathInspectionConfig::TYPE_DIR, 'short/directory', 50);

        // Longest directory should win
        static::assertGreaterThan(0, $configA->compare($configB));
        static::assertLessThan(0, $configB->compare($configA));
    }
}
