<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Model\Metric;

class Failure
{
    public const GLOBAL_COVERAGE_TOO_LOW     = 1;
    public const CUSTOM_COVERAGE_TOO_LOW     = 2;
    public const UNNECESSARY_CUSTOM_COVERAGE = 3;

    /** @var FileMetric */
    private $metric;

    /** @var int */
    private $minimumCoverage;

    /** @var int */
    private $reason;

    public function __construct(FileMetric $metric, int $minimumCoverage, int $reason)
    {
        $this->metric          = $metric;
        $this->minimumCoverage = $minimumCoverage;
        $this->reason          = $reason;
    }

    public function getMetric(): FileMetric
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
