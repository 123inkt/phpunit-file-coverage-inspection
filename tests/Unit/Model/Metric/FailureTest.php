<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Model\Metric;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Failure::class)]
class FailureTest extends TestCase
{
    use AccessorPairAsserter;

    public function testAccessors(): void
    {
        static::assertAccessorPairs(Failure::class);
    }
}
