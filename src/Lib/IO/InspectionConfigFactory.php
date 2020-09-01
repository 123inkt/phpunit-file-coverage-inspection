<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Lib\IO;

use DigitalRevolution\CodeCoverageInspection\Lib\Utility\XMLUtil;
use DigitalRevolution\CodeCoverageInspection\Model\Config\FileInspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use DOMDocument;
use DOMXPath;
use RuntimeException;

class InspectionConfigFactory
{
    public static function fromDOMDocument(string $basePath, DOMDocument $doc): InspectionConfig
    {
        $xpath       = new DOMXpath($doc);
        $minCoverage = self::getMinimumCoverage($xpath);

        // find all custom coverage files
        $files     = [];
        $fileNodes = $xpath->query("/phpcci/custom-coverage/file");
        if ($fileNodes !== false) {
            foreach ($fileNodes as $item) {
                $path            = (string)XMLUtil::getAttribute($item, 'path');
                $minimumCoverage = (int)XMLUtil::getAttribute($item, 'min');
                $files[$path]    = new FileInspectionConfig($path, $minimumCoverage);
            }
        }

        return new InspectionConfig($basePath, $minCoverage, $files);
    }

    private static function getMinimumCoverage(DOMXpath $xpath): int
    {
        // find global minimum coverage setting
        $nodes = $xpath->query("/phpcci");
        if ($nodes === false || $nodes->count() === 0) {
            throw new RuntimeException('Missing `phpcci` in configuration file');
        }

        $node = $nodes->item(0);
        if ($node === null) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException('Missing attributes on `phpcci`');
            // @codeCoverageIgnoreEnd
        }

        return (int)XMLUtil::getAttribute($node, 'min-coverage');
    }
}
