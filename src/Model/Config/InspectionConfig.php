<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Model\Config;

class InspectionConfig
{
    /** @var int */
    private $minimumCoverage;

    /** @var bool */
    private $isUncoveredAllowed;

    /** @var FileInspectionConfig[] */
    private $customCoverage;

    /** @var string */
    private $basePath;

    /**
     * @param FileInspectionConfig[] $customCoverage
     */
    public function __construct(string $basePath, int $minimumCoverage, bool $isUncoveredAllowed = false, array $customCoverage = [])
    {
        $this->basePath           = $basePath;
        $this->minimumCoverage    = $minimumCoverage;
        $this->customCoverage     = $customCoverage;
        $this->isUncoveredAllowed = $isUncoveredAllowed;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function getMinimumCoverage(): int
    {
        return $this->minimumCoverage;
    }

    public function isUncoveredAllowed(): bool
    {
        return $this->isUncoveredAllowed;
    }

    public function getFileInspection(string $path): ?FileInspectionConfig
    {
        return $this->customCoverage[$path] ?? null;
    }
}
