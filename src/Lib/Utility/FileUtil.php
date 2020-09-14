<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Lib\Utility;

use RuntimeException;
use SplFileInfo;

class FileUtil
{
    public static function getRelativePath(string $filepath, string $basePath): string
    {
        if (strpos($filepath, $basePath) === 0) {
            $filepath = substr($filepath, strlen($basePath));
        }

        return $filepath;
    }

    /**
     * @param mixed|null $path
     */
    public static function getFile($path): SplFileInfo
    {
        if (is_string($path) === false) {
            throw new RuntimeException('File path is missing');
        }

        return new SplFileInfo($path);
    }

    /**
     * @param mixed|null $path
     */
    public static function getExistingFile($path): SplFileInfo
    {
        $fileInfo = self::getFile($path);
        if ($fileInfo->isFile() === false) {
            throw new RuntimeException('File is missing or is a directory: ' . $path);
        }

        return $fileInfo;
    }

    public static function writeFile(SplFileInfo $file, string $content): void
    {
        $concurrentDirectory = $file->getPath();
        if (!is_dir($concurrentDirectory) && !mkdir($concurrentDirectory, 0777, true) && !is_dir($concurrentDirectory)) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException(sprintf('Failed to created directory "%s".', $concurrentDirectory));
            // @codeCoverageIgnoreEnd
        }

        $success = @file_put_contents($file->getPathname(), $content);
        if ($success === false) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException(sprintf('Failed to write to file "%s".', $file->getPathname()));
            // @codeCoverageIgnoreEnd
        }
    }
}
