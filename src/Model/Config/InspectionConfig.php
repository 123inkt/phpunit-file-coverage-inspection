<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Model\Config;

class InspectionConfig
{
    private int  $minimumCoverage;
    private bool $uncoveredAllowed;
    /** @var array<string, FileInspectionConfig> */
    private array  $customCoverage;
    private string $basePath;

    /**
     * @param array<string, FileInspectionConfig> $customCoverage
     */
    public function __construct(string $basePath, int $minimumCoverage, bool $uncoveredAllowed = false, array $customCoverage = [])
    {
        $this->basePath         = $basePath;
        $this->minimumCoverage  = $minimumCoverage;
        $this->customCoverage   = $customCoverage;
        $this->uncoveredAllowed = $uncoveredAllowed;
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
        return $this->uncoveredAllowed;
    }

    public function getFileInspection(string $path): ?FileInspectionConfig
    {
        foreach (array_keys($this->customCoverage) as $baselinePath) {
            if (str_ends_with($path, $baselinePath)) {
                return $this->customCoverage[$baselinePath];
            }
        }

        return null;
    }
}
