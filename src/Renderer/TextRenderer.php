<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Renderer;

use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;

class TextRenderer
{
    /**
     * @param Failure[] $failures
     */
    public function render(InspectionConfig $config, array $failures): string
    {
        $maxFilePathLength = 0;
        $lines             = [];

        // gather data
        foreach ($failures as $failure) {
            $file              = sprintf('%s:%d', $failure->getMetric()->getFilepath(), $failure->getLineNumber());
            $lines[]           = [
                'file'  => $file,
                'error' => RendererHelper::renderReason($config, $failure)
            ];
            $maxFilePathLength = max($maxFilePathLength, strlen($file));
        }

        // render
        $result = '';
        foreach ($lines as $line) {
            $result .= sprintf('%-' . ($maxFilePathLength + 10) . 's%s' . PHP_EOL, $line['file'], $line['error']);
        }

        return $result;
    }
}
