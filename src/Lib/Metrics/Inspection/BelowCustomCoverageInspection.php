<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection;

use DigitalRevolution\CodeCoverageInspection\Model\Config\FileInspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;

/**
 * File coverage is below custom coverage
 */
class BelowCustomCoverageInspection extends AbstractInspection
{
    public function inspect(?FileInspectionConfig $fileConfig, FileMetric $metric): ?Failure
    {
        if ($fileConfig !== null && $metric->getCoverage() < $fileConfig->getMinimumCoverage()) {
            return new Failure($metric, $fileConfig->getMinimumCoverage(), Failure::CUSTOM_COVERAGE_TOO_LOW);
        }

        return null;
    }
}
