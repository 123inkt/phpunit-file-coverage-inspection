<?php
declare(strict_types=1);

namespace DR\CodeCoverageInspection\Model\Config;

class InspectionConfig
{
    /** @var int */
    private $minimumCoverage;

    /** @var FileInspectionConfig[] */
    private $customCoverage;

    /** @var string */
    private $basePath;

    /**
     * @param FileInspectionConfig[] $customCoverage
     */
    public function __construct(string $basePath, int $minimumCoverage, array $customCoverage = [])
    {
        $this->basePath        = $basePath;
        $this->minimumCoverage = $minimumCoverage;
        $this->customCoverage  = $customCoverage;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function getMinimumCoverage(): int
    {
        return $this->minimumCoverage;
    }

    public function getFileInspection(string $path): ?FileInspectionConfig
    {
        return $this->customCoverage[$path] ?? null;
    }
}
