<?php
declare(strict_types=1);

namespace DR\CodeCoverageInspection\Renderer;

use DR\CodeCoverageInspection\Model\Config\InspectionConfig;
use DR\CodeCoverageInspection\Model\Metric\Failure;
use RuntimeException;
use XMLWriter;

class CheckStyleRenderer
{
    /**
     * @param Failure[] $failures
     */
    public function render(InspectionConfig $config, array $failures): string
    {
        $out = new XMLWriter();
        $out->openMemory();
        $out->startDocument("1.0", "UTF-8");
        $out->setIndent(true);
        $out->setIndentString(" ");
        $out->startElement('checkstyle');
        $out->writeAttribute('version', '3.5.5');

        foreach ($failures as $failure) {
            $message = $this->formatReason($config, $failure);

            $out->startElement('file');
            $out->writeAttribute('name', $failure->getMetric()->getFilepath());

            $out->startElement('error');
            $out->writeAttribute('line', (string)1);
            $out->writeAttribute('column', (string)0);
            $out->writeAttribute('severity', 'error');
            $out->writeAttribute('message', $message);
            $out->writeAttribute('source', 'phpunit-coverage-inspection');
            $out->endElement();

            $out->endElement(/* file */);
        }

        $out->endElement(/* checkstyle */);

        return $out->flush();
    }

    private function formatReason(InspectionConfig $config, Failure $failure): string
    {
        switch ($failure->getReason()) {
            case Failure::GLOBAL_COVERAGE_TOO_LOW:
                $message = "Project per file coverage is configured at %s%%. Current coverage is at %s%%. Improve coverage for this class.";

                return sprintf($message, (string)$failure->getMinimumCoverage(), (string)$failure->getMetric()->getCoverage());

            case Failure::CUSTOM_COVERAGE_TOO_LOW:
                $message = "Custom file coverage is configured at %s%%. Current coverage is at %s%%. Improve coverage for this class.";

                return sprintf($message, (string)$failure->getMinimumCoverage(), (string)$failure->getMetric()->getCoverage());

            case Failure::UNNECESSARY_CUSTOM_COVERAGE:
                $message = "A custom file coverage is configured at %s%%, but the current file coverage %s%% exceeds the project coverage %s%%. ";
                $message .= "Remove `%s` from phpcci.xml custom-coverage rules.";

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
