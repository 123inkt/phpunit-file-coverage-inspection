<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Renderer;

use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
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
            $message = RendererHelper::renderReason($config, $failure);

            $out->startElement('file');
            $out->writeAttribute('name', $failure->getMetric()->getFilepath());

            $out->startElement('error');
            $out->writeAttribute('line', (string)$failure->getLineNumber());
            $out->writeAttribute('column', "0");
            $out->writeAttribute('severity', 'error');
            $out->writeAttribute('message', $message);
            $out->writeAttribute('source', 'phpunit-file-coverage-inspection');
            $out->endElement();

            $out->endElement(/* file */);
        }

        $out->endElement(/* checkstyle */);

        /** @var int|string $result */
        $result = $out->flush();

        return (string)$result;
    }
}
