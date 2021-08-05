<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Model\Config;

class FileInspectionConfig
{
    private string $path;
    private int $minimumCoverage;

    public function __construct(string $path, int $minimumCoverage)
    {
        $this->path            = $path;
        $this->minimumCoverage = $minimumCoverage;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getMinimumCoverage(): int
    {
        return $this->minimumCoverage;
    }
}
