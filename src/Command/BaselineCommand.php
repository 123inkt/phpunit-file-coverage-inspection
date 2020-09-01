<?php
declare(strict_types=1);

namespace DR\CodeCoverageInspection\Command;

use DR\CodeCoverageInspection\Lib\IO\DOMDocumentFactory;
use DR\CodeCoverageInspection\Lib\IO\MetricsFactory;
use DR\CodeCoverageInspection\Lib\Metrics\MetricsAnalyzer;
use DR\CodeCoverageInspection\Lib\Utility\FileUtil;
use DR\CodeCoverageInspection\Model\Config\InspectionConfig;
use DR\CodeCoverageInspection\Renderer\ConfigFileRenderer;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @codeCoverageIgnore
 */
class BaselineCommand extends Command
{
    protected function configure(): void
    {
        $this->setName("baseline")
            ->setDescription("Generate phpcci.xml based on a given coverage.xml")
            ->addArgument('coverage', InputOption::VALUE_REQUIRED, 'Path to phpunit\'s coverage.xml')
            ->addArgument('config', InputOption::VALUE_OPTIONAL, 'Path to write the configuration file. Defaults to phpcci.xml')
            ->addOption('baseDir', '', InputOption::VALUE_REQUIRED, 'Base directory from where to measure relative config paths');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configArgument = $input->getArgument('config');
        if (is_array($configArgument) && count($configArgument) > 0) {
            $configArgument = reset($configArgument);
        } else {
            $configArgument = getcwd() . '/phpcci.xml';
        }

        $outputPath       = new SplFileInfo($configArgument);
        $basePath         = $input->getOption('baseDir') ?? $outputPath->getPath() . '/';
        $coverageFilePath = FileUtil::getExistingFile($input->getArgument('coverage'), 'coverage.xml filepath');

        if (is_string($basePath) === false) {
            $output->writeln("Base path is not valid");

            return Command::FAILURE;
        }

        // default to 100% coverage
        $config  = new InspectionConfig($basePath, 100);
        $metrics = MetricsFactory::getMetrics(DOMDocumentFactory::getDOMDocument($coverageFilePath));

        // analyzer
        $failures = (new MetricsAnalyzer($metrics, $config))->analyze();

        // write to file
        FileUtil::writeFile($outputPath, (new ConfigFileRenderer())->render($failures, $config));

        $output->writeln('Config successfully written to: ' . $outputPath->getPathname());

        return Command::SUCCESS;
    }
}
