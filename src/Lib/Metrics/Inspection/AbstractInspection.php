<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Lib\Metrics\Inspection;

use DigitalRevolution\CodeCoverageInspection\Model\Config\PathInspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;

abstract class AbstractInspection
{
    protected InspectionConfig $config;

    public function __construct(InspectionConfig $config)
    {
        $this->config = $config;
    }

    abstract public function inspect(?PathInspectionConfig $fileConfig, FileMetric $metric): ?Failure;
}
