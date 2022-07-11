<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Model\Config;

class IgnoreUncoveredMethodFile
{
    private string $filepath;

    public function __construct(string $filepath)
    {
        // normalize slashes
        $this->filepath = str_replace('\\', '/', $filepath);
    }

    public function getFilepath(): string
    {
        return $this->filepath;
    }
}
