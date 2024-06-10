<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Lib\IO;

use DigitalRevolution\CodeCoverageInspection\Lib\Utility\XMLUtil;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\FileMetric;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\MethodMetric;
use DOMDocument;
use DOMNode;
use DOMNodeList;
use DOMXPath;

class MetricsFactory
{
    private const COVERAGE_PERCENTAGE_PRECISION = 2;

    /**
     *  Get metrics information from coverage.xml files
     *
     * @param DOMDocument[] $documents
     *
     * @return array<string, FileMetric>
     */
    public static function getFilesMetrics(array $documents): array
    {
        /** @var array<string, FileMetric> $metrics */
        $metrics = [];

        foreach ($documents as $document) {
            $foundMetrics = self::getFileMetrics($document);
            foreach ($foundMetrics as $metric) {
                $existingMetric = $metrics[$metric->getFilepath()] ?? null;
                if ($existingMetric === null) {
                    $metrics[$metric->getFilepath()] = $metric;
                    continue;
                }

                if ($existingMetric->getCoverage() === 100.0) {
                    continue;
                }

                if ($metric->getCoverage() === 100.0) {
                    $metrics[$metric->getFilepath()] = $metric;
                    continue;
                }

                self::mergeFileMetrics($existingMetric, $metric);
            }
        }

        return $metrics;
    }

    /**
     * Get metrics information from coverage.xml file
     *
     * @return array<string, FileMetric>
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
            $statementsNodes    = $xpath->query('line[@type="stmt"]', $parentNode);
            $statements         = $statementsNodes === false ? 0 : count($statementsNodes);
            $coveredStatements  = (int)XMLUtil::getAttribute($domMetric, 'coveredstatements');
            $coveragePercentage = $statements === 0 ? 100 : round($coveredStatements / $statements * 100, self::COVERAGE_PERCENTAGE_PRECISION);

            // gather metrics per method
            $methodMetrics = self::getMethodMetrics($xpath, $parentNode);

            $coveredStatements = self::getCoveredStatements($statementsNodes);

            $metrics[$filename] = new FileMetric($filename, $statements, $coveragePercentage, $methodMetrics, $coveredStatements);
        }

        return $metrics;
    }

    /**
     * @return array<string, MethodMetric>
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
            $complexity = (int)XMLUtil::getAttribute($methodNode, 'complexity');
            if ($complexity === 0) {
                continue;
            }
            $methodName = XMLUtil::getAttribute($methodNode, 'name') ?? '';
            $lineNumber = (int)XMLUtil::getAttribute($methodNode, 'num');
            $count      = (int)XMLUtil::getAttribute($methodNode, 'count');

            $metrics[$methodName] = new MethodMetric($methodName, $lineNumber, $count);
        }

        return $metrics;
    }

    /**
     * @param DOMNodeList<DOMNode>|false $statementNodes
     *
     * @return int[]
     */
    private static function getCoveredStatements(DOMNodeList|false $statementNodes): array
    {
        if ($statementNodes === false || count($statementNodes) === 0) {
            return [];
        }

        $coveredStatements = [];
        foreach ($statementNodes as $node) {
            $count = (int)XMLUtil::getAttribute($node, 'count');
            if ($count === 0) {
                continue;
            }
            $lineNumber = (int)XMLUtil::getAttribute($node, 'num');

            $coveredStatements[] = $lineNumber;
        }

        return $coveredStatements;
    }

    private static function mergeFileMetrics(FileMetric $existingMetric, FileMetric $metric): void
    {
        $existingMetricMethods = $existingMetric->getMethods();
        $metricMethods         = $metric->getMethods();
        foreach ($metricMethods as $methodName => $methodMetric) {
            if (isset($existingMetricMethods[$methodName]) === false || $existingMetricMethods[$methodName]->getCount() < $methodMetric->getCount()) {
                $existingMetricMethods[$methodName] = $methodMetric;
            }
        }

        $existingCovered = $existingMetric->getCoveredStatements();
        $metricCovered   = $metric->getCoveredStatements();
        $existingMetric->setCoveredStatements(array_merge($existingCovered, array_diff($metricCovered, $existingCovered)));
        $existingMetric->setMethods($existingMetricMethods);
        $coveragePercentage = round(
            count($existingMetric->getCoveredStatements()) / $existingMetric->getStatements() * 100,
            self::COVERAGE_PERCENTAGE_PRECISION
        );
        $existingMetric->setCoverage($coveragePercentage);
    }
}
