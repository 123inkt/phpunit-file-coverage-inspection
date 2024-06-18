<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Functional\Command\InspectCommand;

use DigitalRevolution\CodeCoverageInspection\Command\InspectCommand;
use Exception;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

#[CoversNothing]
class InspectCommandTest extends TestCase
{
    private vfsStreamDirectory $fileSystem;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileSystem = vfsStream::setup('output');
    }

    /**
     * @param string[] $flags
     *
     * @throws Exception|ExceptionInterface
     */
    #[DataProvider('dataProvider')]
    public function testInspectCommand(array $flags, int $exitStatus): void
    {
        // prepare data files
        $configPath   = __DIR__ . '/Data/phpfci.xml';
        $coveragePath = __DIR__ . '/Data/coverage.xml';
        $expected     = str_replace("\r", "", (string)file_get_contents(__DIR__ . '/Data/checkstyle.xml'));
        $output       = $this->fileSystem->url() . '/checkstyle.xml';
        $baseDir      = '/home\workspace';

        // prepare command
        $command = new InspectCommand();
        $input   = new ArgvInput(
            array_merge(['phpfci', '--config', $configPath, '--baseDir', $baseDir, $coveragePath, '--reportCheckstyle', $output], $flags)
        );
        $output  = new ConsoleOutput();

        // run test case
        static::assertSame($exitStatus, $command->run($input, $output));
        static::assertTrue($this->fileSystem->hasChild('checkstyle.xml'));

        // check output
        /** @var vfsStreamFile $resultFile */
        $resultFile = $this->fileSystem->getChild('checkstyle.xml');
        $result     = $resultFile->getContent();

        static::assertSame($expected, $result);
    }

    /**
     * @return array<string, array{0: string[], 1:int}>
     */
    public static function dataProvider(): array
    {
        return [
            'standard exit code'   => [[], Command::SUCCESS],
            'exit code on failure' => [['--exit-code-on-failure'], Command::FAILURE]
        ];
    }
}
