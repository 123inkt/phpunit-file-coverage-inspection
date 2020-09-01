<?php
declare(strict_types=1);

namespace DR\CodeCoverageInspection\Lib\IO;

use DOMDocument;
use LibXMLError;
use RuntimeException;
use SplFileInfo;

class DOMDocumentFactory
{
    public static function getValidatedDOMDocument(SplFileInfo $file, string $schema): DOMDocument
    {
        $dom = self::getDOMDocument($file);

        $prevValue = libxml_use_internal_errors(true);
        if ($dom->schemaValidate($schema) === false) {
            /** @var LibXMLError $error */
            $error = libxml_get_last_error();
            throw new RuntimeException('Xml doesn\'t have the correct format: ' . $file->getPathname() . ':' . $error->message);
        }
        libxml_use_internal_errors($prevValue);

        return $dom;
    }

    public static function getDOMDocument(SplFileInfo $file): DOMDocument
    {
        if ($file->isFile() === false) {
            throw new RuntimeException('Unable to read xml file. File not found: ' . $file->getPathname());
        }

        $dom = new DOMDocument();
        $dom->load($file->getPathname());

        return $dom;
    }
}
