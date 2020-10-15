<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection;

use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\FileMetricAnalyzer;
use DigitalRevolution\CodeCoverageInspection\Model\Config\FileInspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;

/**
 * No custom coverage, and file has a method without any code coverage
 */
class UncoveredMethodsInspection extends AbstractInspection
{
    public function inspect(?FileInspectionConfig $fileConfig, FileMetric $metric): ?Failure
    {
        $uncoveredMethod = FileMetricAnalyzer::getUncoveredMethodMetric($metric);

        if ($fileConfig === null && $uncoveredMethod !== null && $this->config->isUncoveredAllowed() === false) {
            return new Failure($metric, $this->config->getMinimumCoverage(), Failure::MISSING_METHOD_COVERAGE, $uncoveredMethod->getLineNumber());
        }

        return null;
    }
}
