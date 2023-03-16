<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Lib\Config;

use SplFileInfo;

class InspectConfig
{
    private SplFileInfo $coverageFilepath;
    private SplFileInfo $configPath;
    private string      $baseDir;
    private ?string     $reportGitlab;
    private ?string     $reportCheckstyle;
    private ?string     $reportText;
    private bool        $exitCodeOnFailure;

    public function __construct(
        SplFileInfo $coverageFilepath,
        SplFileInfo $configPath,
        string $baseDir,
        ?string $reportGitlab,
        ?string $reportCheckstyle,
        ?string $reportText,
        bool $exitCodeOnFailure
    ) {
        $this->coverageFilepath  = $coverageFilepath;
        $this->configPath        = $configPath;
        $this->baseDir           = $baseDir;
        $this->reportGitlab      = $reportGitlab;
        $this->reportCheckstyle  = $reportCheckstyle;
        $this->reportText        = $reportText;
        $this->exitCodeOnFailure = $exitCodeOnFailure;
    }

    public function getCoverageFilepath(): SplFileInfo
    {
        return $this->coverageFilepath;
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
