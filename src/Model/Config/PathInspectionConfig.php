<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Model\Config;

class PathInspectionConfig
{
    public const TYPE_FILE = 'file';
    public const TYPE_DIR  = 'directory';

    /** @phpstan-var PathInspectionConfig::TYPE_* */
    private string $type;
    private string $path;
    private int    $minimumCoverage;

    /**
     * @phpstan-param PathInspectionConfig::TYPE_* $type
     */
    public function __construct(string $type, string $path, int $minimumCoverage)
    {
        // normalize slashes
        $path = str_replace('\\', '/', $path);
        // trim trailing and leading, and ensure end path has slash.
        if ($type === self::TYPE_DIR) {
            $path = trim($path, '/') . '/';
        }
        $this->type            = $type;
        $this->path            = $path;
        $this->minimumCoverage = $minimumCoverage;
    }

    public function isFile(): bool
    {
        return $this->type === self::TYPE_FILE;
    }

    public function isDirectory(): bool
    {
        return $this->type === self::TYPE_DIR;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getMinimumCoverage(): int
    {
        return $this->minimumCoverage;
    }

    public function compare(PathInspectionConfig $other): int
    {
        if ($this->isFile() && $other->isFile()) {
            return 0;
        }

        // file takes precedence over directory match
        if ($this->isFile() && $other->isDirectory()) {
            return 1;
        }

        if ($this->isDirectory() && $other->isFile()) {
            return -1;
        }

        // remaining: sides are directory. longest path is more specific, therefore wins.
        return strlen($this->getPath()) - strlen($other->getPath());
    }
}
