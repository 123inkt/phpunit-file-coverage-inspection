<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Lib\Utility;

use DigitalRevolution\CodeCoverageInspection\Lib\Utility\FileUtil;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(\DigitalRevolution\CodeCoverageInspection\Lib\Utility\FileUtil::class)]
class FileUtilTest extends TestCase
{
    private vfsStreamDirectory $fileSystem;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileSystem = vfsStream::setup('output');
    }

    public function testFindFilePathMissingDirectory(): void
    {
        static::assertNull(FileUtil::findFilePath('/non-existing-file-path/', []));
    }

    public function testFindFilePathMissingFile(): void
    {
        static::assertNull(FileUtil::findFilePath($this->fileSystem->url(), ['non-existing-file']));
    }

    public function testFindFilePathForExistingFile(): void
    {
        $path     = $this->fileSystem->url();
        $file     = 'existing-file.txt';
        $filepath = $path . '/' . $file;

        // create file
        touch($filepath);

        static::assertSame($filepath, FileUtil::findFilePath($path, ['non-existing-file', $file]));
    }

    public function testGetFileMissingThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('File path is missing');
        FileUtil::getFile(false);
    }

    public function testGetFileShouldPass(): void
    {
        $path = '/a/b/c.txt';
        $file = FileUtil::getFile($path);

        static::assertSame($path, $file->getPathname());
    }

    public function testGetExistingFileThrowsExceptionWhenAbsent(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('File is missing or is a directory');
        FileUtil::getExistingFile($this->fileSystem->url() . 'missing-file.txt');
    }

    public function testGetExistingFileShouldPass(): void
    {
        $filepath = $this->fileSystem->url() . '/existing-file.txt';
        touch($filepath);

        $file = FileUtil::getExistingFile($filepath);
        static::assertSame($filepath, $file->getPathname());
    }

    public function testGetRelativePath(): void
    {
        static::assertSame('b/c.txt', FileUtil::getRelativePath('/a/b/c.txt', '/a/'));
        static::assertSame('/c.txt', FileUtil::getRelativePath('/a/b/c.txt', '/a/b'));
        static::assertSame('/a/b/c.txt', FileUtil::getRelativePath('/a/b/c.txt', '/c/'));
    }

    public function testWriteTo(): void
    {
        $filepath = $this->fileSystem->url() . '/foo/bar/text.txt';
        $content  = 'foobar';
        FileUtil::writeTo($filepath, $content);

        /** @var vfsStreamFile|null $result */
        $result = $this->fileSystem->getChild('foo/bar/text.txt');
        static::assertNotNull($result);
        static::assertSame('foobar', $result->getContent());
    }

    public function testWriteFileToPhpStream(): void
    {
        $filepath = "php://output";
        $content  = 'foobar';

        ob_start();
        FileUtil::writeTo($filepath, $content);
        $result = ob_get_clean();

        static::assertSame($content, $result);
    }
}
