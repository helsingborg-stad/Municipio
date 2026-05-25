<?php

namespace Municipio\Chat\Config;

use AcfService\Contracts\GetField;

class ChatConfig implements ChatConfigInterface
{
    public function __construct(
        private GetField $acfService,
    ) {}

    public function isEnabled(): bool
    {
        return (bool) $this->acfService->getField('chat_enabled', 'option');
    }

    public function isGlobalChatEnabled(): bool
    {
        return (bool) $this->acfService->getField('chat_global_enabled', 'option');
    }

    public function getDefaultAssistant(): ?array
    {
        static $defaultAssistant = null;

        if ($defaultAssistant !== null) {
            return $defaultAssistant;
        }

        static $defaultAssistantName = null;

        if ($defaultAssistantName === null) {
            $defaultAssistantName = $this->acfService->getField('chat_default_assistant', 'option');
        }

        $defaultAssistantArray = array_filter($this->getAssistants(), function ($assistant) use ($defaultAssistantName) {
            return $assistant['name'] === $defaultAssistantName;
        });

        return $defaultAssistant = !empty($defaultAssistantArray) ? array_shift($defaultAssistantArray) : null;
    }

    public function getAssistants(): array
    {
        static $assistants = null;

        if ($assistants !== null) {
            return $assistants;
        }

        $allAssistants = $this->acfService->getField('chat_assistants', 'option');
        return $assistants = !empty($allAssistants) && is_array($allAssistants) ? $allAssistants : [];
    }
}
