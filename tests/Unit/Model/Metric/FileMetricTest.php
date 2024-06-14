<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Model\Metric;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FileMetric::class)]
class FileMetricTest extends TestCase
{
    use AccessorPairAsserter;

    public function testAccessors(): void
    {
        static::assertAccessorPairs(FileMetric::class);
    }
}
