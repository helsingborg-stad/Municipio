<?php

namespace Municipio\ImageConvert\Logging;

class LogEntry
{
    private string $message = '';
    private LogLevel $level = LogLevel::INFO;
    private array $metadata = [];
    private ?object $context = null;

    public function __construct(private Log $logger) {}

    public function setLevel(LogLevel|string $level): self
    {
        $this->level = $level instanceof LogLevel ? $level : LogLevel::from($level);
        return $this;
    }

    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    public function message(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function context(object $context): self
    {
        $this->context = $context;
        return $this;
    }

    public function write(): void
    {
        $this->logger->writeEntry($this);
    }

    // Getters
    public function getMessage(): string { return $this->message; }
    public function getLevel(): LogLevel { return $this->level; }
    public function getMetadata(): array { return $this->metadata; }
    public function getContext(): ?object { return $this->context; }
}