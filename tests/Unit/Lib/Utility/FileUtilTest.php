<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Lib\Utility;

use DigitalRevolution\CodeCoverageInspection\Lib\Utility\FileUtil;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use SplFileInfo;

/**
 * @coversDefaultClass \DigitalRevolution\CodeCoverageInspection\Lib\Utility\FileUtil
 */
class FileUtilTest extends TestCase
{
    /** @var vfsStreamDirectory */
    private $fileSystem;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileSystem = vfsStream::setup('output');
    }

    /**
     * @covers ::findFilePath
     */
    public function testFindFilePathMissingDirectory(): void
    {
        static::assertNull(FileUtil::findFilePath('/non-existing-file-path/', []));
    }

    /**
     * @covers ::findFilePath
     */
    public function testFindFilePathMissingFile(): void
    {
        static::assertNull(FileUtil::findFilePath($this->fileSystem->url(), ['non-existing-file']));
    }

    /**
     * @covers ::findFilePath
     */
    public function testFindFilePathForExistingFile(): void
    {
        $path     = $this->fileSystem->url();
        $file     = 'existing-file.txt';
        $filepath = $path . '/' . $file;

        // create file
        touch($filepath);

        static::assertSame($filepath, FileUtil::findFilePath($path, ['non-existing-file', $file]));
    }

    /**
     * @covers ::getFile
     */
    public function testGetFileMissingThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('File path is missing');
        FileUtil::getFile(false);
    }

    /**
     * @covers ::getFile
     */
    public function testGetFileShouldPass(): void
    {
        $path = '/a/b/c.txt';
        $file = FileUtil::getFile($path);

        static::assertSame($path, $file->getPathname());
    }

    /**
     * @covers ::getExistingFile
     */
    public function testGetExistingFileThrowsExceptionWhenAbsent(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('File is missing or is a directory');
        FileUtil::getExistingFile($this->fileSystem->url() . 'missing-file.txt');
    }

    /**
     * @covers ::getExistingFile
     */
    public function testGetExistingFileShouldPass(): void
    {
        $filepath = $this->fileSystem->url() . '/existing-file.txt';
        touch($filepath);

        $file = FileUtil::getExistingFile($filepath);
        static::assertSame($filepath, $file->getPathname());
    }

    /**
     * @covers ::getRelativePath
     */
    public function testGetRelativePath(): void
    {
        static::assertSame('b/c.txt', FileUtil::getRelativePath('/a/b/c.txt', '/a/'));
        static::assertSame('/c.txt', FileUtil::getRelativePath('/a/b/c.txt', '/a/b'));
        static::assertSame('/a/b/c.txt', FileUtil::getRelativePath('/a/b/c.txt', '/c/'));
    }

    /**
     * @covers ::writeFile
     */
    public function testWriteFile(): void
    {
        $filepath = $this->fileSystem->url() . '/foo/bar/text.txt';
        $content  = 'foobar';
        FileUtil::writeFile(new SplFileInfo($filepath), $content);

        /** @var vfsStreamFile|null $result */
        $result = $this->fileSystem->getChild('foo/bar/text.txt');
        static::assertNotNull($result);
        static::assertSame('foobar', $result->getContent());
    }

    /**
     * @covers ::writeFile
     */
    public function testWriteFileToPhpStream(): void
    {
        $filepath = "php://output";
        $content  = 'foobar';

        ob_start();
        FileUtil::writeFile(new SplFileInfo($filepath), $content);
        $result = ob_get_clean();

        static::assertSame($content, $result);
    }
}
