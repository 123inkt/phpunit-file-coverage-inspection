<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Renderer;

use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use RuntimeException;

class RendererHelper
{
    public static function renderReason(InspectionConfig $config, Failure $failure): string
    {
        switch ($failure->getReason()) {
            case Failure::GLOBAL_COVERAGE_TOO_LOW:
                $message = "Project per file coverage is configured at %s%%. Current coverage is at %s%%. Improve coverage for this class.";

                return sprintf($message, (string)$failure->getMinimumCoverage(), (string)$failure->getMetric()->getCoverage());
            case Failure::CUSTOM_COVERAGE_TOO_LOW:
                $message = "Custom file coverage is configured at %s%%. Current coverage is at %s%%. Improve coverage for this class.";

                return sprintf($message, (string)$failure->getMinimumCoverage(), (string)$failure->getMetric()->getCoverage());
            case Failure::MISSING_METHOD_COVERAGE:
                $message     = "File coverage is above %s%%, but method(s) `%s` has/have no coverage at all.";
                $methodNames = array_map(static fn($method): string => $method->getMethodName(), $failure->getMetric()->getMethods());

                return sprintf($message, (string)$config->getMinimumCoverage(), implode(', ', $methodNames));
            case Failure::UNNECESSARY_CUSTOM_COVERAGE:
                $message = "A custom file coverage is configured at %s%%, but the current file coverage %s%% exceeds the project coverage %s%%. ";
                $message .= "Remove `%s` from phpfci.xml custom-coverage rules.";

                return sprintf(
                    $message,
                    (string)$failure->getMinimumCoverage(),
                    (string)$failure->getMetric()->getCoverage(),
                    (string)$config->getMinimumCoverage(),
                    $failure->getMetric()->getFilepath()
                );
            default:
                throw new RuntimeException('Unknown failure reason: ' . $failure->getReason());
        }
    }
}
