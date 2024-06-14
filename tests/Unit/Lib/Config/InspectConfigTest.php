<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Lib\Config;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DigitalRevolution\CodeCoverageInspection\Lib\Config\InspectConfig;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\DigitalRevolution\CodeCoverageInspection\Lib\Config\InspectConfig::class)]
class InspectConfigTest extends TestCase
{
    use AccessorPairAsserter;

    public function testAccessorPairs(): void
    {
        static::assertAccessorPairs(InspectConfig::class);
    }
}
