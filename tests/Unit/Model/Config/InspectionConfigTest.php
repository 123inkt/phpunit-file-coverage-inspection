<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Model\Config;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DigitalRevolution\AccessorPairConstraint\Constraint\ConstraintConfig;
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
        $config = new ConstraintConfig();
        $config->setExcludedMethods(['getBasePath']);

        static::assertAccessorPairs(InspectionConfig::class, $config);
    }

    /**
     * @covers ::getBasePath
     */
    public function testGetBasePath(): void
    {
        $config = new InspectionConfig('/base\\path', 0, true);
        static::assertSame('/base/path/', $config->getBasePath());
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

        static::assertSame($fileConfig, $config->getPathInspection('foo/bar/file'));
        static::assertSame($fileConfig, $config->getPathInspection('/base/path/foo/bar/file'));
        static::assertSame($fileConfig, $config->getPathInspection('/base/path/foo/bar/path/to/file'));
    }

    /**
     * @covers ::addPathInspection
     * @covers ::getPathInspection
     */
    public function testGetPathInspectionWithFileAndDirectoryInspection(): void
    {
        $fileConfig      = new PathInspectionConfig(PathInspectionConfig::TYPE_FILE, 'foo/bar/file', 50);
        $directoryConfig = new PathInspectionConfig(PathInspectionConfig::TYPE_DIR, 'foo/bar/', 60);
        $config          = new InspectionConfig('/base\\path', 20, false);
        $config->addPathInspection($fileConfig);
        $config->addPathInspection($directoryConfig);

        static::assertSame($directoryConfig, $config->getPathInspection('foo/bar/directory'));
        static::assertSame($fileConfig, $config->getPathInspection('foo/bar/file'));
        static::assertSame($directoryConfig, $config->getPathInspection('/base/path/foo/bar/directory'));
        static::assertSame($fileConfig, $config->getPathInspection('/base/path/foo/bar/file'));
    }

    /**
     * @covers ::addPathInspection
     * @covers ::getPathInspection
     */
    public function testGetPathInspectionBestDirectoryInspection(): void
    {
        $directoryConfigA = new PathInspectionConfig(PathInspectionConfig::TYPE_DIR, 'foo/bar/', 60);
        $directoryConfigB = new PathInspectionConfig(PathInspectionConfig::TYPE_DIR, 'foo/bar/directory', 50);
        $config           = new InspectionConfig('/base\\path', 20, false);
        $config->addPathInspection($directoryConfigA);
        $config->addPathInspection($directoryConfigB);

        static::assertNull($config->getPathInspection('foo/directory'));
        static::assertSame($directoryConfigB, $config->getPathInspection('foo/bar/directory/file'));
        static::assertSame($directoryConfigA, $config->getPathInspection('foo/bar/other/file'));
        static::assertSame($directoryConfigB, $config->getPathInspection('/base/path/foo/bar/directory/file'));
        static::assertSame($directoryConfigA, $config->getPathInspection('/base/path/foo/bar/other/file'));
    }
}
