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
        $xpath = new DOMXpath($doc);
        [$minCoverage, $allowUncoveredMethods] = self::getConfiguration($xpath);

        // find all custom coverage files
        $files     = [];
        $fileNodes = $xpath->query("/phpfci/custom-coverage/file");
        if ($fileNodes !== false) {
            foreach ($fileNodes as $item) {
                $path            = (string)XMLUtil::getAttribute($item, 'path');
                $minimumCoverage = (int)XMLUtil::getAttribute($item, 'min');
                $files[$path]    = new FileInspectionConfig($path, $minimumCoverage);
            }
        }

        return new InspectionConfig($basePath, $minCoverage, $allowUncoveredMethods, $files);
    }

    private static function getConfiguration(DOMXpath $xpath): array
    {
        // find global coverage settings
        $nodes = $xpath->query("/phpfci");
        if ($nodes === false || $nodes->count() === 0) {
            throw new RuntimeException('Missing `phpfci` in configuration file');
        }

        $node = $nodes->item(0);
        if ($node === null) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException('Missing attributes on `phpfci`');
            // @codeCoverageIgnoreEnd
        }

        $minCoverage           = (int)XMLUtil::getAttribute($node, 'min-coverage');
        $allowUncoveredMethods = XMLUtil::getAttribute($node, 'allow-uncovered-methods') === "true";

        return [$minCoverage, $allowUncoveredMethods];
    }
}
