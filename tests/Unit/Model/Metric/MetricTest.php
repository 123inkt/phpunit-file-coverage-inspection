<?php
declare(strict_types=1);

namespace DR\CodeCoverageInspection\Tests\Unit\Model\Metric;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DR\CodeCoverageInspection\Model\Metric\Metric;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DR\CodeCoverageInspection\Model\Metric\Metric
 */
class MetricTest extends TestCase
{
    use AccessorPairAsserter;

    /**
     * @covers ::<public>
     */
    public function testAccessors(): void
    {
        static::assertAccessorPairs(Metric::class);
    }
}
