<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Lib\Utility;

use DOMNode;

class XMLUtil
{
    public static function getAttribute(DOMNode $node, string $attribute): ?string
    {
        if ($node->attributes === null) {
            return null;
        }

        $attributeNode = $node->attributes->getNamedItem($attribute);
        if ($attributeNode === null) {
            return null;
        }

        return $attributeNode->nodeValue;
    }
}
