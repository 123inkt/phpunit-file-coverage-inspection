<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Lib\IO;

use DigitalRevolution\CodeCoverageInspection\Lib\Utility\XMLUtil;
use DigitalRevolution\CodeCoverageInspection\Model\Config\IgnoreUncoveredMethodFile;
use DigitalRevolution\CodeCoverageInspection\Model\Config\PathInspectionConfig;
use DOMNode;
use RuntimeException;

class IgnoreUncoveredMethodFileFactory
{
    public static function createFromNode(DOMNode $item): IgnoreUncoveredMethodFile
    {
        if ($item->nodeName !== PathInspectionConfig::TYPE_FILE) {
            throw new RuntimeException('Invalid node type: ' . $item->nodeName);
        }

        return new IgnoreUncoveredMethodFile((string)XMLUtil::getAttribute($item, 'path'));
    }
}
