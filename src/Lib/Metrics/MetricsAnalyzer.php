<?php
declare(strict_types=1);

namespace DR\CodeCoverageInspection\Lib\Metrics;

use DR\CodeCoverageInspection\Lib\Utility\FileUtil;
use DR\CodeCoverageInspection\Model\Config\InspectionConfig;
use DR\CodeCoverageInspection\Model\Metric\Failure;
use DR\CodeCoverageInspection\Model\Metric\Metric;

class MetricsAnalyzer
{
    /** @var Metric[] */
    private $metrics;

    /** @var InspectionConfig */
    private $config;

    /**
     * @param Metric[] $metrics
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
        $failures        = [];
        $minimumCoverage = $this->config->getMinimumCoverage();

        foreach ($this->metrics as $metric) {
            $filepath   = FileUtil::getRelativePath($metric->getFilepath(), $this->config->getBasePath());
            $fileConfig = $this->config->getFileInspection($filepath);

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

            // custom coverage, but file is already above global coverage
            if ($fileConfig !== null && $metric->getCoverage() >= $minimumCoverage) {
                $failures[] = new Failure($metric, $fileConfig->getMinimumCoverage(), Failure::UNNECESSARY_CUSTOM_COVERAGE);
                continue;
            }
        }

        return $failures;
    }
}
