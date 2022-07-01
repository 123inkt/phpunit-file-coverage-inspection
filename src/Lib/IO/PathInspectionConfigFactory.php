<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Lib\IO;

use DigitalRevolution\CodeCoverageInspection\Lib\Utility\XMLUtil;
use DigitalRevolution\CodeCoverageInspection\Model\Config\PathInspectionConfig;
use DOMNode;
use RuntimeException;

class PathInspectionConfigFactory
{
    public static function createFromNode(DOMNode $item): PathInspectionConfig
    {
        if (in_array($item->nodeName, [PathInspectionConfig::TYPE_FILE, PathInspectionConfig::TYPE_DIR], true) === false) {
            throw new RuntimeException('Invalid node type: ' . $item->nodeName);
        }

        $type            = $item->nodeName;
        $path            = (string)XMLUtil::getAttribute($item, 'path');
        $minimumCoverage = (int)XMLUtil::getAttribute($item, 'min');

        return new PathInspectionConfig($type, $path, $minimumCoverage);
    }
}
