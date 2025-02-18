<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Lib\Metrics;

use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection\AbstractInspection;
use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection\DifferentCustomCoverageInspection;
use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection\BelowGlobalCoverageInspection;
use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection\CustomCoverageAboveGlobalInspection;
use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection\UncoveredMethodsInspection;
use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;

class MetricsAnalyzer
{
    /** @var FileMetric[] */
    private array $metrics;

    private InspectionConfig $config;

    /** @var AbstractInspection[] */
    private array $inspections;

    /**
     * @param FileMetric[] $metrics
     */
    public function __construct(array $metrics, InspectionConfig $config)
    {
        $this->metrics = $metrics;
        $this->config  = $config;

        $this->inspections = [
            new CustomCoverageAboveGlobalInspection($config),
            new DifferentCustomCoverageInspection($config),
            new BelowGlobalCoverageInspection($config),
            new UncoveredMethodsInspection($config)
        ];
    }

    /**
     * @return Failure[] all metrics that failed the minimum configured coverage percentage
     */
    public function analyze(): array
    {
        $failures = [];

        foreach ($this->metrics as $metric) {
            $fileConfig = $this->config->getPathInspection($metric->getFilepath());

            foreach ($this->inspections as $inspection) {
                $failure = $inspection->inspect($fileConfig, $metric);
                if ($failure !== null) {
                    $failures[] = $failure;
                    break;
                }
            }
        }

        return $failures;
    }
}
