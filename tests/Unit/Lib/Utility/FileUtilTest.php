<?php
declare(strict_types=1);

namespace DR\CodeCoverageInspection\Tests\Unit\Lib\Utility;

use DR\CodeCoverageInspection\Lib\Utility\FileUtil;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use SplFileInfo;

/**
 * @coversDefaultClass \DR\CodeCoverageInspection\Lib\Utility\FileUtil
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
     * @covers ::getFile
     */
    public function testGetFileMissingThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('File path is not configured or no default');
        FileUtil::getFile(false, 'test');
    }

    /**
     * @covers ::getFile
     */
    public function testGetFileShouldPass(): void
    {
        $path = '/a/b/c.txt';
        $file = FileUtil::getFile($path, 'test');

        static::assertSame($path, $file->getPathname());
    }

    /**
     * @covers ::getExistingFile
     */
    public function testGetExistingFileThrowsExceptionWhenAbsent(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('File is missing or is a directory');
        FileUtil::getExistingFile($this->fileSystem->url() . 'missing-file.txt', 'test');
    }

    /**
     * @covers ::getExistingFile
     */
    public function testGetExistingFileShouldPass(): void
    {
        $filepath = $this->fileSystem->url() . '/existing-file.txt';
        touch($filepath);

        $file = FileUtil::getExistingFile($filepath, 'test');
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
}
