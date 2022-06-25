<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Model\Config;

class InspectionConfig
{
    private int  $minimumCoverage;
    private bool $uncoveredAllowed;
    /** @var PathInspectionConfig[] */
    private array  $customCoverage = [];
    private string $basePath;

    public function __construct(string $basePath, int $minimumCoverage, bool $uncoveredAllowed = false)
    {
        $this->basePath         = $basePath;
        $this->minimumCoverage  = $minimumCoverage;
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

    public function addPathInspection(PathInspectionConfig $inspectionConfig): self
    {
        $this->customCoverage[] = $inspectionConfig;

        return $this;
    }

    public function getPathInspection(string $path): ?PathInspectionConfig
    {
        foreach ($this->customCoverage as $pathInspectionConfig) {
            $baselinePath = $pathInspectionConfig->getPath();
            if (str_ends_with($path, $baselinePath)) {
                return $pathInspectionConfig;
            }
        }

        return null;
    }
}
