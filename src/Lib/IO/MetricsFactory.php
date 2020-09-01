<?php
declare(strict_types=1);

namespace DR\CodeCoverageInspection\Lib\IO;

use DOMDocument;
use DOMNode;
use DOMXPath;
use DR\CodeCoverageInspection\Lib\Utility\XMLUtil;
use DR\CodeCoverageInspection\Model\Metric\Metric;

class MetricsFactory
{
    /**
     * Get metrics information from coverage.xml file
     *
     * @return Metric[]
     */
    public static function getMetrics(DOMDocument $document): array
    {
        $metrics = [];
        $xpath   = new DOMXPath($document);

        // find all file metrics and determine coverage
        $domMetrics = $xpath->query('/coverage/project//file/metrics');
        if ($domMetrics === false) {
            // @codeCoverageIgnoreStart
            return [];
            // @codeCoverageIgnoreEnd
        }

        foreach ($domMetrics as $domMetric) {
            /** @var DOMNode $parentNode */
            $parentNode = $domMetric->parentNode;
            $filename   = (string)XMLUtil::getAttribute($parentNode, 'name');

            // calculate coverage
            $statements        = (int)XMLUtil::getAttribute($domMetric, 'statements');
            $coveredStatements = (int)XMLUtil::getAttribute($domMetric, 'coveredstatements');
            $coverage          = $statements === 0 ? 100 : round($coveredStatements / $statements * 100, 2);

            $metrics[] = new Metric($filename, $coverage);
        }

        return $metrics;
    }
}
