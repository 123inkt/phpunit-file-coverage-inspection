<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Command;

use DigitalRevolution\CodeCoverageInspection\Lib\IO\DOMDocumentFactory;
use DigitalRevolution\CodeCoverageInspection\Lib\IO\InspectionConfigFactory;
use DigitalRevolution\CodeCoverageInspection\Lib\IO\MetricsFactory;
use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\MetricsAnalyzer;
use DigitalRevolution\CodeCoverageInspection\Lib\Utility\FileUtil;
use DigitalRevolution\CodeCoverageInspection\Renderer\CheckStyleRenderer;
use DigitalRevolution\CodeCoverageInspection\Renderer\GitlabErrorRenderer;
use InvalidArgumentException;
use JsonException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @codeCoverageIgnore
 */
class InspectCommand extends Command
{
    private const CONFIG_FILES = ['phpfci.xml', 'phpfci.xml.dist'];

    protected function configure(): void
    {
        $this->setName("inspect")
            ->setDescription("PHPUnit code coverage inspection")
            ->addArgument('coverage', InputOption::VALUE_REQUIRED, 'Path to phpunit\'s coverage.xml')
            ->addArgument('output', InputOption::VALUE_REQUIRED, 'Path to write inspections report file to')
            ->addOption('config', 'c', InputOption::VALUE_REQUIRED, 'Path to configuration file. Optional')
            ->addOption('baseDir', '', InputOption::VALUE_REQUIRED, 'Base directory from where to determine the relative config paths')
            ->addOption('report', '', InputOption::VALUE_REQUIRED, 'output format, either checkstyle or gitlab', 'checkstyle')
            ->addOption('exit-code-on-failure', '', InputOption::VALUE_NONE, 'If failures, exit with failure exit code');
    }

    /**
     * @throws JsonException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configPath       = FileUtil::getExistingFile($input->getOption('config') ?? FileUtil::findFilePath((string)getcwd(), self::CONFIG_FILES));
        $baseDir          = $input->getOption('baseDir') ?? $configPath->getPath() . '/';
        $coverageFilePath = FileUtil::getExistingFile($input->getArgument('coverage'));
        $outputFilePath   = FileUtil::getFile($input->getArgument('output'));
        $schema           = dirname(__DIR__, 2) . '/resources/phpfci.xsd';

        if (is_string($baseDir) === false) {
            $output->writeln("--baseDir argument is not valid. Expecting string argument");

            return Command::FAILURE;
        }

        // gather data
        $domConfig = DOMDocumentFactory::getValidatedDOMDocument($configPath, $schema);
        $config    = InspectionConfigFactory::fromDOMDocument($baseDir, $domConfig);
        $metrics   = MetricsFactory::getFileMetrics(DOMDocumentFactory::getDOMDocument($coverageFilePath));

        if (count($metrics) === 0) {
            $output->writeln("No metrics found in coverage file: " . $coverageFilePath->getPathname());

            return Command::FAILURE;
        }

        // analyze
        $failures = (new MetricsAnalyzer($metrics, $config))->analyze();

        // write output
        switch ($input->getOption('report')) {
            case 'checkstyle':
                FileUtil::writeFile($outputFilePath, (new CheckStyleRenderer())->render($config, $failures));
                break;
            case 'gitlab':
                FileUtil::writeFile($outputFilePath, (new GitlabErrorRenderer())->render($config, $failures));
                break;
            default:
                throw new InvalidArgumentException('Invalid report argument: ' . $input->getOption('report'));
        }

        // raise exit code on failure
        if (count($failures) > 0 && $input->hasOption('exit-code-on-failure')) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
