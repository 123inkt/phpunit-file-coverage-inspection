<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Lib\IO;

use DigitalRevolution\CodeCoverageInspection\Lib\Utility\XMLUtil;
use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use DOMDocument;
use DOMXPath;
use RuntimeException;

class InspectionConfigFactory
{
    public static function fromDOMDocument(string $basePath, DOMDocument $doc): InspectionConfig
    {
        $xpath            = new DOMXpath($doc);
        $inspectionConfig = self::getInspectionConfig($basePath, $xpath);

        // find all custom coverage node
        $nodes = array_merge(XMLUtil::query($xpath, "/phpfci/custom-coverage/directory"), XMLUtil::query($xpath, "/phpfci/custom-coverage/file"));
        foreach ($nodes as $node) {
            $inspectionConfig->addPathInspection(PathInspectionConfigFactory::createFromNode($node));
        }

        // find all ignore uncovered method nodes
        $nodes = XMLUtil::query($xpath, "/phpfci/ignore-uncovered-methods/file");
        foreach ($nodes as $node) {
            $inspectionConfig->addIgnoreUncoveredMethodFile(IgnoreUncoveredMethodFileFactory::createFromNode($node));
        }

        return $inspectionConfig;
    }

    private static function getInspectionConfig(string $basePath, DOMXpath $xpath): InspectionConfig
    {
        // find global coverage settings
        $nodes = XMLUtil::query($xpath, "/phpfci");
        if (count($nodes) === 0) {
            throw new RuntimeException('Missing `phpfci` in configuration file');
        }

        $node               = reset($nodes);
        $minCoverage        = (int)XMLUtil::getAttribute($node, 'min-coverage');
        $isUncoveredAllowed = XMLUtil::getAttribute($node, 'allow-uncovered-methods') === "true";

        return new InspectionConfig($basePath, $minCoverage, $isUncoveredAllowed);
    }
}
