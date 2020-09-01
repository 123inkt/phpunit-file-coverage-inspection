<?php
declare(strict_types=1);

namespace DR\CodeCoverageInspection\Model\Config;

class FileInspectionConfig
{
    /** @var string */
    private $path;

    /** @var int */
    private $minimumCoverage;

    public function __construct(string $path, int $minimumCoverage)
    {
        $this->path            = $path;
        $this->minimumCoverage = $minimumCoverage;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return int
     */
    public function getMinimumCoverage(): int
    {
        return $this->minimumCoverage;
    }
}
