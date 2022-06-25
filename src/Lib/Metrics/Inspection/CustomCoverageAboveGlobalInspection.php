<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection;

use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\FileMetricAnalyzer;
use DigitalRevolution\CodeCoverageInspection\Model\Config\PathInspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;

/**
 * If a custom coverage is set, it should be below global coverage setting
 */
class CustomCoverageAboveGlobalInspection extends AbstractInspection
{
    public function inspect(?PathInspectionConfig $fileConfig, FileMetric $metric): ?Failure
    {
        $uncoveredMethod = FileMetricAnalyzer::getUncoveredMethodMetric($metric);
        if ($fileConfig === null || $uncoveredMethod !== null) {
            return null;
        }

        $globalCoverage = $this->config->getMinimumCoverage();
        $customCoverage = $fileConfig->getMinimumCoverage();

        // custom coverage is lower than global coverage, and file is above global coverage
        if ($customCoverage < $globalCoverage && $metric->getCoverage() >= $globalCoverage) {
            return new Failure($metric, $fileConfig->getMinimumCoverage(), Failure::UNNECESSARY_CUSTOM_COVERAGE);
        }

        return null;
    }
}
