<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection;

use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\FileMetricAnalyzer;
use DigitalRevolution\CodeCoverageInspection\Model\Config\FileInspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;

/**
 * If a custom coverage is set, it should be below global coverage setting
 */
class CustomCoverageAboveGlobalInspection extends AbstractInspection
{
    public function inspect(?FileInspectionConfig $fileConfig, FileMetric $metric): ?Failure
    {
        $uncoveredMethod = FileMetricAnalyzer::getUncoveredMethodMetric($metric);

        // custom coverage, but file is already above global coverage
        if ($fileConfig !== null && $uncoveredMethod === null && $metric->getCoverage() >= $this->config->getMinimumCoverage()) {
            return new Failure($metric, $fileConfig->getMinimumCoverage(), Failure::UNNECESSARY_CUSTOM_COVERAGE);
        }

        return null;
    }
}
