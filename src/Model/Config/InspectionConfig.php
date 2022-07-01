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
        $this->basePath         = rtrim(str_replace('\\', '/', $basePath), '/') . '/';
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
        // subtract basePath from path
        $relativePath = (string)preg_replace('#^' . preg_quote($this->basePath, '#') . '#', '', $path);

        $bestConfig = null;
        foreach ($this->customCoverage as $config) {
            $baselinePath = $config->getPath();

            if ($config->isFile() && $relativePath !== $baselinePath) {
                continue;
            }

            if ($config->isDirectory() && str_starts_with($relativePath, $baselinePath) === false) {
                continue;
            }

            // determine which rule has the highest priority
            if ($bestConfig === null || $config->compare($bestConfig) > 0) {
                $bestConfig = $config;
            }
        }

        return $bestConfig;
    }
}
