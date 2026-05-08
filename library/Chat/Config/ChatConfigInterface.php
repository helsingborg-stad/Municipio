<?php

namespace Municipio\Chat\Config;

interface ChatConfigInterface
{
    public function isEnabled(): bool;
    public function isGlobalChatEnabled(): bool;
    public function getDefaultAssistant(): ?array;
    public function getAssistants(): array;
}
