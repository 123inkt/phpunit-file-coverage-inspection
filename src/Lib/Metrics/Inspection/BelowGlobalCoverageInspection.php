<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection;

use DigitalRevolution\CodeCoverageInspection\Model\Config\FileInspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;

/**
 * No custom coverage, and file is below global minimum coverage
 */
class BelowGlobalCoverageInspection extends AbstractInspection
{
    public function inspect(?FileInspectionConfig $fileConfig, FileMetric $metric): ?Failure
    {
        if ($fileConfig === null && $metric->getCoverage() < $this->config->getMinimumCoverage()) {
            return new Failure($metric, $this->config->getMinimumCoverage(), Failure::GLOBAL_COVERAGE_TOO_LOW);
        }

        return null;
    }
}
