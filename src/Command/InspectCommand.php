<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Command;

use DigitalRevolution\CodeCoverageInspection\Lib\Config\ConfigFactory;
use DigitalRevolution\CodeCoverageInspection\Lib\Config\ConfigViolation;
use DigitalRevolution\CodeCoverageInspection\Lib\IO\DOMDocumentFactory;
use DigitalRevolution\CodeCoverageInspection\Lib\IO\InspectionConfigFactory;
use DigitalRevolution\CodeCoverageInspection\Lib\IO\MetricsFactory;
use DigitalRevolution\CodeCoverageInspection\Lib\Metrics\MetricsAnalyzer;
use DigitalRevolution\CodeCoverageInspection\Lib\Utility\FileUtil;
use DigitalRevolution\CodeCoverageInspection\Renderer\CheckStyleRenderer;
use DigitalRevolution\CodeCoverageInspection\Renderer\GitlabErrorRenderer;
use DigitalRevolution\CodeCoverageInspection\Renderer\TextRenderer;
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
    private ConfigFactory $configFactory;
    private string        $schemaPath;

    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->configFactory = new ConfigFactory();
        $this->schemaPath    = dirname(__DIR__, 2) . '/resources/phpfci.xsd';
    }

    protected function configure(): void
    {
        $this->setName("inspect")
            ->setDescription("PHPUnit code coverage inspection")
            ->addArgument('coverage', InputOption::VALUE_REQUIRED, 'Path to phpunit\'s coverage.xml')
            ->addOption('config', 'c', InputOption::VALUE_REQUIRED, 'Path to configuration file. Optional')
            ->addOption('baseDir', '', InputOption::VALUE_REQUIRED, 'Base directory from where to determine the relative config paths')
            ->addOption('reportGitlab', '', InputOption::VALUE_OPTIONAL, 'Gitlab output format. To file or if absent to stdout', false)
            ->addOption('reportCheckstyle', '', InputOption::VALUE_OPTIONAL, 'Checkstyle output format. To file or if absent to stdout', false)
            ->addOption('reportText', '', InputOption::VALUE_OPTIONAL, 'User-friendly text output format. To file or if absent to stdout', false)
            ->addOption('exit-code-on-failure', '', InputOption::VALUE_NONE, 'If failures, exit with failure exit code');
    }

    /**
     * @throws JsonException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputConfig = $this->configFactory->createInspectConfig($input);
        if ($inputConfig instanceof ConfigViolation) {
            $output->writeln($inputConfig->getMessage());

            return Command::FAILURE;
        }

        // gather data
        $domConfig = DOMDocumentFactory::getValidatedDOMDocument($inputConfig->getConfigPath(), $this->schemaPath);
        $config    = InspectionConfigFactory::fromDOMDocument($inputConfig->getBaseDir(), $domConfig);
        $metrics   = MetricsFactory::getFileMetrics(DOMDocumentFactory::getDOMDocument($inputConfig->getCoverageFilepath()));

        if (count($metrics) === 0) {
            $output->writeln("No metrics found in coverage file: " . $inputConfig->getCoverageFilepath());

            return Command::FAILURE;
        }

        // analyze
        $failures = (new MetricsAnalyzer($metrics, $config))->analyze();

        // write output
        if ($inputConfig->getReportGitlab() !== null) {
            FileUtil::writeTo($inputConfig->getReportGitlab(), (new GitlabErrorRenderer())->render($config, $failures));
        }
        if ($inputConfig->getReportCheckstyle() !== null) {
            FileUtil::writeTo($inputConfig->getReportCheckstyle(), (new CheckStyleRenderer())->render($config, $failures));
        }
        if ($inputConfig->getReportText() !== null) {
            FileUtil::writeTo($inputConfig->getReportText(), (new TextRenderer())->render($config, $failures));
        }

        // raise exit code on failure
        if (count($failures) > 0 && $inputConfig->isExitCodeOnFailure()) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
