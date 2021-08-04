<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Model\Metric;

class FileMetric
{
    private string $filepath;
    private float  $coverage;
    /** @var MethodMetric[] */
    private array $methods;

    /**
     * @param MethodMetric[] $methods
     */
    public function __construct(string $filepath, float $coverage, array $methods)
    {
        $this->filepath = $filepath;
        $this->coverage = $coverage;
        $this->methods  = $methods;
    }

    public function getFilepath(): string
    {
        return $this->filepath;
    }

    public function getCoverage(): float
    {
        return $this->coverage;
    }

    /**
     * @return  MethodMetric[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }
}
