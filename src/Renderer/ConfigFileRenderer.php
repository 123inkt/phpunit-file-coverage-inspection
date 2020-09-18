<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Renderer;

use DigitalRevolution\CodeCoverageInspection\Lib\Utility\FileUtil;
use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use XMLWriter;

class ConfigFileRenderer
{
    /**
     * @param Failure[] $failures
     */
    public function render(array $failures, InspectionConfig $config): string
    {
        $out = new XMLWriter();
        $out->openMemory();
        $out->startDocument("1.0", "UTF-8");
        $out->setIndent(true);
        $out->setIndentString("    ");
        $out->startElement('phpfci');
        $out->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $out->writeAttribute('xsi:noNamespaceSchemaLocation', 'vendor/digitalrevolution/phpunit-file-coverage-inspection/resources/phpfci.xsd');
        $out->writeAttribute('min-coverage', (string)$config->getMinimumCoverage());

        if (count($failures) > 0) {
            $out->startElement('custom-coverage');

            foreach ($failures as $failure) {
                $filepath = FileUtil::getRelativePath($failure->getMetric()->getFilepath(), $config->getBasePath());

                $out->startElement('file');
                $out->writeAttribute('path', $filepath);
                $out->writeAttribute('min', (string)floor($failure->getMetric()->getCoverage()));
                $out->endElement(/* file */);
            }
            $out->endElement(/* custom-coverage>*/);
        }

        $out->endElement(/* phpfci */);

        return $out->flush();
    }
}
