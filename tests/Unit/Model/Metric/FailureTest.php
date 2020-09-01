<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Model\Metric;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure
 * @covers ::__construct
 */
class FailureTest extends TestCase
{
    use AccessorPairAsserter;

    /**
     * @covers ::<public>
     */
    public function testAccessors(): void
    {
        static::assertAccessorPairs(Failure::class);
    }
}
