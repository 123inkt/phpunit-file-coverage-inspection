<?php
declare(strict_types=1);

namespace DR\CodeCoverageInspection\Tests\Unit\Model\Config;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DR\CodeCoverageInspection\Model\Config\FileInspectionConfig;
use DR\CodeCoverageInspection\Model\Config\InspectionConfig;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DR\CodeCoverageInspection\Model\Config\InspectionConfig
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
     * @covers ::getFileInspection
     */
    public function testGetFileInspection(): void
    {
        $fileConfig = new FileInspectionConfig('foobar', 50);
        $config     = new InspectionConfig('/base/path', 20, ['foobar' => $fileConfig]);

        static::assertNull($config->getFileInspection('invalid'));
        static::assertSame($fileConfig, $config->getFileInspection('foobar'));
    }
}
