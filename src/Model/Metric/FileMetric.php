<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Model\Metric;

class FileMetric
{
    /**
     * @param array<string, MethodMetric> $methods
     * @param int[]                       $coveredStatements
     */
    public function __construct(
        private readonly string $filepath,
        private readonly int $statements,
        private float $coverage,
        private array $methods,
        private array $coveredStatements
    ) {
    }

    public function getFilepath(): string
    {
        return $this->filepath;
    }

    public function getStatements(): int
    {
        return $this->statements;
    }

    public function getCoverage(): float
    {
        return $this->coverage;
    }

    public function setCoverage(float $coverage): self
    {
        $this->coverage = $coverage;

        return $this;
    }

    /**
     * @return array<string, MethodMetric>
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param array<string, MethodMetric> $methods
     */
    public function setMethods(array $methods): self
    {
        $this->methods = $methods;

        return $this;
    }

    /**
     * @return int[] $coveredStatements
     */
    public function getCoveredStatements(): array
    {
        return $this->coveredStatements;
    }

    /**
     * @param int[] $coveredStatements
     */
    public function setCoveredStatements(array $coveredStatements): self
    {
        $this->coveredStatements = $coveredStatements;

        return $this;
    }
}
