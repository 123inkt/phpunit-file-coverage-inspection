<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Lib\Config;

use DigitalRevolution\CodeCoverageInspection\Lib\Utility\FileUtil;
use Symfony\Component\Console\Input\InputInterface;

class ConfigFactory
{
    private const CONFIG_FILES = ['phpfci.xml', 'phpfci.xml.dist'];

    /**
     * @return ConfigViolation|InspectConfig
     */
    public function createInspectConfig(InputInterface $input)
    {
        $configPath       = FileUtil::getExistingFile($input->getOption('config') ?? FileUtil::findFilePath((string)getcwd(), self::CONFIG_FILES));
        $coverageFilepath = FileUtil::getExistingFile($input->getArgument('coverage'));
        $baseDir          = $input->getOption('baseDir') ?? $configPath->getPath();

        if (is_string($baseDir) === false) {
            return new ConfigViolation('--base-dir expecting a value string as argument');
        }

        $reportGitlab     = $this->getReport($input, 'reportGitlab');
        $reportCheckstyle = $this->getReport($input, 'reportCheckstyle');
        $reportText       = $this->getReport($input, 'reportText');

        if ($reportGitlab instanceof ConfigViolation) {
            return $reportGitlab;
        }
        if ($reportCheckstyle instanceof ConfigViolation) {
            return $reportCheckstyle;
        }
        if ($reportText instanceof ConfigViolation) {
            return $reportText;
        }

        $reports = array_filter([$reportGitlab, $reportCheckstyle, $reportText]);
        if ($reports !== array_unique($reports)) {
            return new ConfigViolation('Two or more reports output to the same destination');
        }

        $exitCodeOnFailure = $input->getOption('exit-code-on-failure') !== false;

        return new InspectConfig($coverageFilepath, $configPath, $baseDir, $reportGitlab, $reportCheckstyle, $reportText, $exitCodeOnFailure);
    }

    /**
     * @return ConfigViolation|string|null
     */
    private function getReport(InputInterface $input, string $option)
    {
        $report = $input->getOption($option) ?? 'php://stdout';
        if ($report !== false && is_string($report) === false) {
            return new ConfigViolation('--' . $option . ' expecting the value to absent or string argument');
        }

        return $report === false ? null : $report;
    }
}
