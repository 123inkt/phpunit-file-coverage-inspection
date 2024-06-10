<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Model\Metric;

class MethodMetric
{
    private string $methodName;
    private int $lineNumber;
    private int $count;
    private array $linesDetails = [];

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

    public function addLineDetail(int $lineNumber, int $count): self
    {
        $this->linesDetails[$lineNumber] = $count;

        return $this;
    }

    public function merge(MethodMetric $otherMetric): self
    {
        if ($this->getCount() < $otherMetric->getCount()) {
            $this->count = $otherMetric->getCount();
        }

        return $this;
    }
}
