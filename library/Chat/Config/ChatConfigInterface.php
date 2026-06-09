<?php

namespace Municipio\Chat\Config;

interface ChatConfigInterface
{
    public function isEnabled(): bool;

    public function isGlobalChatEnabled(): bool;

    public function getDefaultAssistant(): ?array;

    public function getAssistantForActiveQuery(): ?array;

    public function getAssistants(): array;

    public function isPresidioEnabled(): bool;

    public function getPresidioAnalyzerHost(): ?string;

    public function getPresidioAnonymizerHost(): ?string;

    public function getPresidioLanguage(): ?string;

    public function getPresidioAnonymizerConfig(): ?array;

    public function getPresidioAllowList(): array;
}
