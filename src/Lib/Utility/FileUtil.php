<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Lib\Utility;

use RuntimeException;
use SplFileInfo;

class FileUtil
{
    /**
     * Find the first file occurrence in the given path
     *
     * @param string[] $fileNames
     */
    public static function findFilePath(string $path, array $fileNames): ?string
    {
        if (is_dir($path) === false) {
            return null;
        }

        foreach ($fileNames as $fileName) {
            $filepath = $path . '/' . $fileName;
            if (is_file($filepath)) {
                return $filepath;
            }
        }

        return null;
    }

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
        $dir = $file->getPath();
        if ($dir !== '' && !is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException(sprintf('Failed to create directory "%s".', $dir));
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
