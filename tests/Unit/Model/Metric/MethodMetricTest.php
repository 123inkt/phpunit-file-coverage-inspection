<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Model\Metric;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\MethodMetric;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MethodMetric::class)]
class MethodMetricTest extends TestCase
{
    use AccessorPairAsserter;

    public function testAccessors(): void
    {
        static::assertAccessorPairs(MethodMetric::class);
    }
}
