<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Model\Config;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DigitalRevolution\CodeCoverageInspection\Model\Config\PathInspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig
 * @covers ::__construct
 */
class InspectionConfigTest extends TestCase
{
    use AccessorPairAsserter;

    /**
     * @covers ::<public>
     */
    public function testAccessors(): void
    {
        static::assertAccessorPairs(InspectionConfig::class);
    }

    /**
     * @covers ::addPathInspection
     * @covers ::getPathInspection
     */
    public function testGetPathInspectionWithFileInspection(): void
    {
        $fileConfig = new PathInspectionConfig(PathInspectionConfig::TYPE_FILE, 'foobar', 50);
        $config     = new InspectionConfig('/base/path', 20, false);
        $config->addPathInspection($fileConfig);

        static::assertNull($config->getPathInspection('invalid'));
        static::assertSame($fileConfig, $config->getPathInspection('foobar'));
        static::assertSame($fileConfig, $config->getPathInspection('/base/path/foobar'));
    }

    /**
     * @covers ::addPathInspection
     * @covers ::getPathInspection
     */
    public function testGetPathInspectionWithDirectoryInspection(): void
    {
        $fileConfig = new PathInspectionConfig(PathInspectionConfig::TYPE_DIR, 'foo/bar/', 50);
        $config     = new InspectionConfig('/base\\path', 20, false);
        $config->addPathInspection($fileConfig);

        static::assertNull($config->getPathInspection('invalid'));
        static::assertSame($fileConfig, $config->getPathInspection('foo/bar/file'));
        static::assertSame($fileConfig, $config->getPathInspection('/base/path/foo/bar/file'));
        static::assertSame($fileConfig, $config->getPathInspection('/base/path/foo/bar/path/to/file'));
    }
}
