<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Functional\Command\InspectCommand;

use DigitalRevolution\CodeCoverageInspection\Command\InspectCommand;
use Exception;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

#[CoversNothing]
class InspectCommandMultiFileTest extends TestCase
{
    private vfsStreamDirectory $fileSystem;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileSystem = vfsStream::setup('output');
    }

    /**
     * @throws Exception|ExceptionInterface
     */
    public function testInspectCommand(): void
    {
        // prepare data files
        $configPath         = __DIR__ . '/Data/phpfci.xml';
        $coveragePath       = __DIR__ . '/Data/coverage-multi1.xml';
        $coverageCustomPath = __DIR__ . '/Data/coverage-multi2.xml';
        $expected           = str_replace("\r", "", (string)file_get_contents(__DIR__ . '/Data/checkstyle-multi.xml'));
        $output             = $this->fileSystem->url() . '/checkstyle-multi.xml';
        $baseDir            = '/home\workspace';

        // prepare command
        $command = new InspectCommand();
        $input   = new ArgvInput(
            array_merge(
                [
                    'phpfci',
                    '--config',
                    $configPath,
                    '--baseDir',
                    $baseDir,
                    $coveragePath,
                    $coverageCustomPath,
                    '--reportCheckstyle',
                    $output
                ],
                []
            )
        );
        $output  = new ConsoleOutput();

        // run test case
        static::assertSame(Command::SUCCESS, $command->run($input, $output));
        static::assertTrue($this->fileSystem->hasChild('checkstyle-multi.xml'));

        // check output
        /** @var vfsStreamFile $resultFile */
        $resultFile = $this->fileSystem->getChild('checkstyle-multi.xml');
        $result     = $resultFile->getContent();

        static::assertSame($expected, $result);
    }
}
