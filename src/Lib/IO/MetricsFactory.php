<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Lib\IO;

use DigitalRevolution\CodeCoverageInspection\Lib\Utility\XMLUtil;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\MethodMetric;
use DOMDocument;
use DOMNode;
use DOMXPath;

class MetricsFactory
{
    private const COVERAGE_PERCENTAGE_PRECISION = 2;

    /**
     *  Get metrics information from coverage.xml files
     *
     * @param DOMDocument[] $documents
     *
     * @return FileMetric[]
     */
    public static function getFilesMetrics(array $documents): array
    {
        $metrics = [];

        foreach ($documents as $document) {
            $foundMetrics = self::getFileMetrics($document);
            foreach ($foundMetrics as $metric) {
                if (isset($metrics[$metric->getFilepath()])) {
                    $previousMetric = $metrics[$metric->getFilepath()];
                    if ($previousMetric->getCoverage() > $metric->getCoverage()) {
                        continue;
                    }
                }
                $metrics[$metric->getFilepath()] = $metric;
            }
        }

        return $metrics;
    }

    /**
     * Get metrics information from coverage.xml file
     *
     * @return FileMetric[]
     */
    public static function getFileMetrics(DOMDocument $document): array
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
            $filename   = str_replace('\\', '/', (string)XMLUtil::getAttribute($parentNode, 'name'));

            // calculate coverage
            $statements         = (int)XMLUtil::getAttribute($domMetric, 'statements');
            $coveredStatements  = (int)XMLUtil::getAttribute($domMetric, 'coveredstatements');
            $coveragePercentage = $statements === 0 ? 100 : round($coveredStatements / $statements * 100, self::COVERAGE_PERCENTAGE_PRECISION);

            // gather metrics per method
            $methodMetrics = self::getMethodMetrics($xpath, $parentNode);

            $metrics[] = new FileMetric($filename, $coveragePercentage, $methodMetrics);
        }

        return $metrics;
    }

    /**
     * @return MethodMetric[]
     */
    public static function getMethodMetrics(DOMXPath $xpath, DOMNode $fileNode): array
    {
        // get all line entries
        $methodNodes = $xpath->query('line[@type="method"]', $fileNode);
        if ($methodNodes === false || count($methodNodes) === 0) {
            return [];
        }

        $metrics = [];
        foreach ($methodNodes as $methodNode) {
            $methodName = XMLUtil::getAttribute($methodNode, 'name') ?? '';
            $lineNumber = (int)XMLUtil::getAttribute($methodNode, 'num');
            $count      = (int)XMLUtil::getAttribute($methodNode, 'count');

            $metrics[] = new MethodMetric($methodName, $lineNumber, $count);
        }

        return $metrics;
    }
}
