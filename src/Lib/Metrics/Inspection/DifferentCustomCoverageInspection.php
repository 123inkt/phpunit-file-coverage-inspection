<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection;

use DigitalRevolution\CodeCoverageInspection\Model\Config\PathInspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;

/**
 * File coverage is below custom coverage
 */
class DifferentCustomCoverageInspection extends AbstractInspection
{
    public function inspect(?PathInspectionConfig $fileConfig, FileMetric $metric): ?Failure
    {
        if ($fileConfig !== null && (int)floor($metric->getCoverage()) < $fileConfig->getMinimumCoverage()) {
            return new Failure($metric, $fileConfig->getMinimumCoverage(), Failure::CUSTOM_COVERAGE_TOO_LOW);
        }

        if ($fileConfig !== null && (int)floor($metric->getCoverage()) > $fileConfig->getMinimumCoverage()) {
            return new Failure($metric, $fileConfig->getMinimumCoverage(), Failure::CUSTOM_COVERAGE_TOO_HIGH);
        }

        return null;
    }
}
