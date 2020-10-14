<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Model\Metric;

class MethodMetric
{
    /** @var string */
    private $methodName;

    /** @var int */
    private $lineNumber;

    /** @var int */
    private $count;

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
