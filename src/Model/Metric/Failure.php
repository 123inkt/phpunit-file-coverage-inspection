<?php
declare(strict_types=1);

namespace DR\CodeCoverageInspection\Model\Metric;

class Failure
{
    public const GLOBAL_COVERAGE_TOO_LOW     = 1;
    public const CUSTOM_COVERAGE_TOO_LOW     = 2;
    public const UNNECESSARY_CUSTOM_COVERAGE = 3;

    /** @var Metric */
    private $metric;

    /** @var int */
    private $minimumCoverage;

    /** @var int */
    private $reason;

    public function __construct(Metric $metric, int $minimumCoverage, int $reason)
    {
        $this->metric          = $metric;
        $this->minimumCoverage = $minimumCoverage;
        $this->reason          = $reason;
    }

    public function getMetric(): Metric
    {
        return $this->metric;
    }

    public function getMinimumCoverage(): int
    {
        return $this->minimumCoverage;
    }

    public function getReason(): int
    {
        return $this->reason;
    }
}
