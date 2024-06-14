<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Functional\Command\BaselineCommand;

use DigitalRevolution\CodeCoverageInspection\Command\BaselineCommand;
use Exception;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;

#[CoversNothing]
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
        // prepare data files
        $coveragePath = __DIR__ . '/Data/coverage.xml';
        $expected     = str_replace("\r", "", file_get_contents(__DIR__ . '/Data/phpfci.xml'));
        $output       = $this->fileSystem->url() . '/phpfci.xml';
        $baseDir      = '/home/workspace/';

        // prepare command
        $command = new BaselineCommand();
        $input   = new ArgvInput(['phpfci', '--baseDir', $baseDir, $coveragePath, $output]);
        $output  = new BufferedOutput();

        // run test case
        static::assertSame(Command::SUCCESS, $command->run($input, $output));
        static::assertTrue($this->fileSystem->hasChild('phpfci.xml'));

        // check output
        /** @var vfsStreamFile $resultFile */
        $resultFile = $this->fileSystem->getChild('phpfci.xml');
        $result     = $resultFile->getContent();

        static::assertSame($expected, $result);
    }
}
