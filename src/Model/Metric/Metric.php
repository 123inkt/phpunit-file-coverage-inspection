<?php
declare(strict_types=1);

namespace DR\CodeCoverageInspection\Model\Metric;

class Metric
{
    /** @var string */
    private $filepath;

    /** @var float */
    private $coverage;

    public function __construct(string $filepath, float $coverage)
    {
        $this->filepath = $filepath;
        $this->coverage = $coverage;
    }

    public function getFilepath(): string
    {
        return $this->filepath;
    }

    public function getCoverage(): float
    {
        return $this->coverage;
    }
}
