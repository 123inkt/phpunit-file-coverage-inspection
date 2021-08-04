<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Model\Metric;

class MethodMetric
{
    private string $methodName;
    private int    $lineNumber;
    private int    $count;

    public function __construct(string $methodName, int $lineNumber, int $count)
    {
        $this->methodName = $methodName;
        $this->lineNumber = $lineNumber;
        $this->count      = $count;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
