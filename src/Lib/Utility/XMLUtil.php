<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Lib\Utility;

use DOMNode;
use DOMXPath;

class XMLUtil
{
    /**
     * @return DOMNode[]
     */
    public static function query(DOMXpath $xpath, string $query): array
    {
        $nodes = $xpath->query($query);

        /** @phpstan-var DOMNode[] */
        return $nodes === false ? [] : iterator_to_array($nodes, false);
    }

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
