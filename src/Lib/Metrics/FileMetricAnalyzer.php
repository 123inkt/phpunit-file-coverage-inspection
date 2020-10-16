<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Lib\Metrics;

use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\MethodMetric;

class FileMetricAnalyzer
{
    public static function getUncoveredMethodMetric(FileMetric $metric): ?MethodMetric
    {
        foreach ($metric->getMethods() as $method) {
            if ($method->getCount() === 0) {
                return $method;
            }
        }

        return null;
    }
}
