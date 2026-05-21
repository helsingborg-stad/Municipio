<?php

namespace Municipio\Chat\Render;

use Municipio\Chat\Config\ChatConfigInterface;
use Municipio\Helper\Image;
use WpService\Contracts\__;

class ChatRenderConfig implements ChatRenderConfigInterface {
    private ?array $assistant = null;
    private ?array $avatar = null;

    public function __construct(
        private __ $wpService,
        private ChatConfigInterface $config,
        private string $view,
        private string $assistantName,
        private ?string $wrapperAttributes = '',
    ) {
    }

    public function getView(): string
    {
        return $this->view;
    }

    public function getAssistantName(): string
    {
        return $this->assistantName;
    }

    public function getAssistant(): ?array
    {
        if ($this->assistant !== null) {
            return $this->assistant;
        }

        return $this->assistant = $this->getAssistantFromString();
    }

    public function getWrapperAttributes(): ?string
    {
        return $this->wrapperAttributes;
    }

    public function getGreetingsPhrase(): ?string
    {
        $assistant = $this->getAssistant();

        if (empty($assistant) || empty($assistant['greetings_phrase'])) {
            return null;
        }

        return $assistant['greetings_phrase'];
    }

    public function getAttributeList(): array
    {
        $attributeList = [
            'data-js-chat-assistant' => $this->getAssistantName(),
        ];

        if (!empty($this->getGreetingsPhrase())) {
            $attributeList['data-js-chat-greetings-phrase'] = $this->getGreetingsPhrase();
        }

        return $attributeList;
    }

    public function getLang(): array {
        return [
            'chat' => $this->wpService->__('Chat', 'municipio'),
            'close' => $this->wpService->__('Close', 'municipio'),
            'send' => $this->wpService->__('Send', 'municipio'),
            'placeholder' => $this->wpService->__('Write your question here', 'municipio'),
            'newConversation' => $this->wpService->__('New conversation', 'municipio'),
            'like' => $this->wpService->__('Like', 'municipio'),
            'dislike' => $this->wpService->__('Dislike', 'municipio'),
        ];
    }

    public function getAvatar(): ?array
    {
        $assistant = $this->getAssistant();

        if ($this->avatar !== null) {
            return $this->avatar;
        }

        if (empty($assistant) || empty($assistant['avatar'])) {
            return null;
        }

        return $this->avatar = Image::getImageAttachmentData($assistant['avatar'], [150, 150]);
    }

    private function getAssistantFromString(): ?array
    {
        $allAssistants = $this->config->getAssistants();

        if (empty($allAssistants)) {
            return null;
        }

        if ($this->getAssistantName() === 'Default') {
            return $this->config->getDefaultAssistant();
        }

        $filteredAssistant = array_filter($allAssistants, fn($a) => $a['name'] === ($this->getAssistantName() ?? null));
        return reset($filteredAssistant) ?: null;
    }
}