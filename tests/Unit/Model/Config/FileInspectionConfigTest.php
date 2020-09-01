<?php
declare(strict_types=1);

namespace DR\CodeCoverageInspection\Tests\Unit\Model\Config;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DR\CodeCoverageInspection\Model\Config\FileInspectionConfig;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DR\CodeCoverageInspection\Model\Config\FileInspectionConfig
 */
class FileInspectionConfigTest extends TestCase
{
    use AccessorPairAsserter;

    /**
     * @covers ::<public>
     */
    public function testAccessors(): void
    {
        static::assertAccessorPairs(FileInspectionConfig::class);
    }
}
