<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Command;

use DigitalRevolution\CodeCoverageInspection\Lib\Config\ConfigFactory;
use DigitalRevolution\CodeCoverageInspection\Lib\IO\DOMDocumentFactory;
use DigitalRevolution\CodeCoverageInspection\Lib\IO\MetricsFactory;
use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\MetricsAnalyzer;
use DigitalRevolution\CodeCoverageInspection\Lib\Utility\FileUtil;
use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Renderer\ConfigFileRenderer;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
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
            ->setDescription("Generate phpfci.xml based on a given coverage.xml")
            ->addArgument('coverage', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to phpunit\'s coverage.xml')
            ->addOption('config', '', InputOption::VALUE_REQUIRED, 'Path to write the configuration file')
            ->addOption('threshold', '', InputOption::VALUE_REQUIRED, 'Minimum coverage threshold, defaults to 100', 100)
            ->addOption('baseDir', '', InputOption::VALUE_REQUIRED, 'Base directory from where to determine the relative config paths');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configArgument = $input->getOption('config');
        if (is_array($configArgument)) {
            if (count($configArgument) === 0) {
                throw new RuntimeException('Missing config argument');
            }
            $configArgument = reset($configArgument);
        }

        $outputPath        = new SplFileInfo((string)$configArgument);
        $baseDir           = $input->getOption('baseDir') ?? $outputPath->getPath();
        $threshold         = $input->getOption('threshold');
        $coveragesFilepath = [];
        $coverageArgument  = $input->getArgument('coverage');
        if (is_array($coverageArgument) === false) {
            $output->writeln('Coverage argument should be an array');
            return Command::FAILURE;
        }
        foreach ($coverageArgument as $coverageFilepath) {
            $coveragesFilepath[] = FileUtil::getExistingFile($coverageFilepath);
        }

        if (is_string($baseDir) === false) {
            $output->writeln("--baseDir argument is not valid. Expecting string argument");

            return Command::FAILURE;
        }

        if (is_numeric($threshold) === false) {
            $output->writeln("--threshold should be a numeric value");

            return Command::FAILURE;
        }

        // default to 100% coverage
        $config       = new InspectionConfig($baseDir, (int)$threshold, false);
        $domDocuments = [];
        foreach ($coveragesFilepath as $coverageFilepath) {
            $domDocuments[] = DOMDocumentFactory::getDOMDocument($coverageFilepath);
        }
        $metrics = MetricsFactory::getFilesMetrics($domDocuments);

        // analyzer
        $failures = (new MetricsAnalyzer($metrics, $config))->analyze();

        // write to file
        FileUtil::writeTo($outputPath->getPathname(), (new ConfigFileRenderer())->render($failures, $config));

        $output->writeln('Config successfully written to: ' . $outputPath->getPathname());

        return Command::SUCCESS;
    }
}
