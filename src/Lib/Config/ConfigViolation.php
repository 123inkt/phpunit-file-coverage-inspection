<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Lib\Config;

class ConfigViolation
{
    private string $message;
    /** @var string[] */
    private array $parameters;

    public function __construct(string $message, string ...$parameters)
    {
        $this->message    = $message;
        $this->parameters = $parameters;
    }

    public function getMessage(): string
    {
        return sprintf($this->message, ...$this->parameters);
    }
}
