<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Lib\Config;

use SplFileInfo;

class InspectConfig
{
    /** @var SplFileInfo[] */
    private array $coveragesFilepath;
    private SplFileInfo $configPath;
    private string $baseDir;
    private ?string $reportGitlab;
    private ?string $reportCheckstyle;
    private ?string $reportText;
    private bool $exitCodeOnFailure;

    /**
     * @param SplFileInfo[] $coveragesFilepath
     */
    public function __construct(
        array $coveragesFilepath,
        SplFileInfo $configPath,
        string $baseDir,
        ?string $reportGitlab,
        ?string $reportCheckstyle,
        ?string $reportText,
        bool $exitCodeOnFailure
    ) {
        $this->coveragesFilepath = $coveragesFilepath;
        $this->configPath        = $configPath;
        $this->baseDir           = $baseDir;
        $this->reportGitlab      = $reportGitlab;
        $this->reportCheckstyle  = $reportCheckstyle;
        $this->reportText        = $reportText;
        $this->exitCodeOnFailure = $exitCodeOnFailure;
    }

    /**
     * @return SplFileInfo[]
     */
    public function getCoveragesFilepath(): array
    {
        return $this->coveragesFilepath;
    }

    public function getConfigPath(): SplFileInfo
    {
        return $this->configPath;
    }

    public function getBaseDir(): string
    {
        return $this->baseDir;
    }

    public function getReportGitlab(): ?string
    {
        return $this->reportGitlab;
    }

    public function getReportCheckstyle(): ?string
    {
        return $this->reportCheckstyle;
    }

    public function getReportText(): ?string
    {
        return $this->reportText;
    }

    public function isExitCodeOnFailure(): bool
    {
        return $this->exitCodeOnFailure;
    }
}
