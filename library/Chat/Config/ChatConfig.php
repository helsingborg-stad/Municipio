<?php

namespace Municipio\Chat\Config;

use AcfService\Contracts\GetField;
use WpService\Contracts\DetermineLocale;

class ChatConfig implements ChatConfigInterface
{
    public function __construct(
        private DetermineLocale $wpService,
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
        $defaultAssistantName = $this->acfService->getField('chat_default_assistant', 'option');

        if (!is_string($defaultAssistantName)) {
            return null;
        }

        $defaultAssistantArray = array_filter($this->getAssistants(), function ($assistant) use ($defaultAssistantName) {
            return $assistant['name'] === $defaultAssistantName;
        });

        return !empty($defaultAssistantArray) ? array_shift($defaultAssistantArray) : null;
    }

    public function getAssistants(): array
    {
        $allAssistants = $this->acfService->getField('chat_assistants', 'option');
        return !empty($allAssistants) && is_array($allAssistants) ? $allAssistants : [];
    }

    public function isPresidioEnabled(): bool
    {
        return (bool) $this->acfService->getField('chat_presidio_enabled', 'option');
    }

    public function getPresidio(): array
    {
        $group = $this->acfService->getField('chat_presidio', 'option');
        return is_array($group) ? $group : [];
    }

    public function getPresidioAnalyzerHost(): ?string
    {
        $presidio = $this->getPresidio();
        $host = $presidio['analyzer_host'] ?? null;
        return is_string($host) ? $host : null;
    }

    public function getPresidioAnonymizerHost(): ?string
    {
        $presidio = $this->getPresidio();
        $host = $presidio['anonymizer_host'] ?? null;
        return is_string($host) ? $host : null;
    }

    public function getPresidioLanguage(): ?string
    {
        $presidio = $this->getPresidio();
        $supportedLanguages = $presidio['supported_languages'] ?? null;

        if (is_array($supportedLanguages)) {
            $locale = $this->wpService->determineLocale();
            $twoLetterLocale = substr($locale, 0, 2);
            foreach ($supportedLanguages as $language) {
                if (strtolower($language['language']) === strtolower($twoLetterLocale)) {
                    return $language['language'];
                }
            }
            $fallback = $supportedLanguages[0]['language'] ?? null;
            if (is_string($fallback)) {
                return $fallback;
            }
        }
        return null;
    }

    public function getPresidioAnonymizerConfig(): ?array
    {
        $presidio = $this->getPresidio();
        $json = $presidio['anonymizer_config'] ?? null;
        if (is_string($json)) {
            $decoded = json_decode($json, true);
            return is_array($decoded) ? $decoded : null;
        }
        return null;
    }

    public function getPresidioAllowList(): array
    {
        $presidio = $this->getPresidio();
        $allowList = $presidio['allow_list'] ?? null;

        if (!is_array($allowList)) {
            return [];
        }

        $words = array_map(
            fn($row) => is_array($row) ? $row['word'] ?? null : null,
            $allowList,
        );

        foreach ($words as $word) {
            if (!is_string($word)) {
                return [];
            }
        }

        return array_values($words);
    }
}
