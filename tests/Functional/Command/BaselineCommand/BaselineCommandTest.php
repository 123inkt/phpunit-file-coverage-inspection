<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Functional\Command\BaselineCommand;

use DigitalRevolution\CodeCoverageInspection\Command\BaselineCommand;
use DigitalRevolution\CodeCoverageInspection\Command\InspectCommand;
use Exception;
use Hoa\Iterator\Buffer;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\ConsoleOutput;

class BaselineCommandTest extends TestCase
{

    /** @var vfsStreamDirectory */
    private $fileSystem;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileSystem = vfsStream::setup('output');
    }

    /**
     * @throws Exception
     */
    public function testBaselineCommand(): void
    {
        // baseline coverage.xml --baseDir /home/jenkins/build/workspace/0_TP_DRCore_code_coverage_checks/

        // prepare data files
        $coveragePath = __DIR__ . '/Data/coverage.xml';
        $expected     = str_replace("\r", "", file_get_contents(__DIR__ . '/Data/phpcci.xml'));
        $output       = $this->fileSystem->url() . '/phpcci.xml';
        $baseDir      = '/home/workspace/';

        // prepare command
        $command = new BaselineCommand();
        $input   = new ArgvInput(['phpcci', '--baseDir', $baseDir, $coveragePath, $output]);
        $output  = new BufferedOutput();

        // run test case
        static::assertSame(Command::SUCCESS, $command->run($input, $output));
        static::assertTrue($this->fileSystem->hasChild('phpcci.xml'));

        // check output
        /** @var vfsStreamFile $resultFile */
        $resultFile = $this->fileSystem->getChild('phpcci.xml');
        $result     = $resultFile->getContent();

        static::assertSame($expected, $result);
    }
}
