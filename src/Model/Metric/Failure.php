<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Model\Metric;

class Failure
{
    public const GLOBAL_COVERAGE_TOO_LOW     = 1;
    public const CUSTOM_COVERAGE_TOO_LOW     = 2;
    public const UNNECESSARY_CUSTOM_COVERAGE = 3;
    public const MISSING_METHOD_COVERAGE     = 4;

    private FileMetric $metric;
    private int $minimumCoverage;
    private int $reason;
    private int $lineNumber;

    public function __construct(FileMetric $metric, int $minimumCoverage, int $reason, int $lineNumber = 1)
    {
        $this->metric          = $metric;
        $this->minimumCoverage = $minimumCoverage;
        $this->reason          = $reason;
        $this->lineNumber      = $lineNumber;
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

    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }
}
