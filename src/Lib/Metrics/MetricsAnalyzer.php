<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Lib\Metrics;

use DigitalRevolution\CodeCoverageInspection\Lib\Utility\FileUtil;
use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;

class MetricsAnalyzer
{
    /** @var FileMetric[] */
    private $metrics;

    /** @var InspectionConfig */
    private $config;

    /**
     * @param FileMetric[] $metrics
     */
    public function __construct(array $metrics, InspectionConfig $config)
    {
        $this->metrics = $metrics;
        $this->config  = $config;
    }

    /**
     * @return Failure[] all metrics that failed the minimum configured coverage percentage
     */
    public function analyze(): array
    {
        $failures              = [];
        $minimumCoverage       = $this->config->getMinimumCoverage();
        $allowUncoveredMethods = $this->config->isAllowUncoveredMethods();

        foreach ($this->metrics as $metric) {
            $filepath        = FileUtil::getRelativePath($metric->getFilepath(), $this->config->getBasePath());
            $fileConfig      = $this->config->getFileInspection($filepath);
            $uncoveredMethod = FileMetricAnalyzer::getUncoveredMethodMetric($metric);

            // file is below custom coverage
            if ($fileConfig !== null && $metric->getCoverage() < $fileConfig->getMinimumCoverage()) {
                $failures[] = new Failure($metric, $fileConfig->getMinimumCoverage(), Failure::CUSTOM_COVERAGE_TOO_LOW);
                continue;
            }

            // no custom coverage, and file is below global minimum coverage
            if ($fileConfig === null && $metric->getCoverage() < $minimumCoverage) {
                $failures[] = new Failure($metric, $minimumCoverage, Failure::GLOBAL_COVERAGE_TOO_LOW);
                continue;
            }

            // no custom coverage, and file has a method without any code coverage
            if ($fileConfig === null && $allowUncoveredMethods === false && $uncoveredMethod !== null) {
                $failures[] = new Failure($metric, $minimumCoverage, Failure::MISSING_METHOD_COVERAGE, $uncoveredMethod->getLineNumber());
                continue;
            }

            // custom coverage, but file is already above global coverage
            if ($fileConfig !== null && $uncoveredMethod === null && $metric->getCoverage() >= $minimumCoverage) {
                $failures[] = new Failure($metric, $fileConfig->getMinimumCoverage(), Failure::UNNECESSARY_CUSTOM_COVERAGE);
                continue;
            }
        }

        return $failures;
    }
}
