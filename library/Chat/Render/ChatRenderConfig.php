<?php

namespace Municipio\Chat\Render;

use Municipio\Chat\Config\ChatConfigInterface;
use Municipio\Helper\Image;
use WpService\Contracts\__;

class ChatRenderConfig implements ChatRenderConfigInterface {
    public function __construct(
        private __ $wpService,
        private ChatConfigInterface $config,
        private string $view,
        private ?string $assistantName = null,
        private ?string $wrapperAttributes = '',
    ) {}

    public function getView(): string
    {
        return $this->view;
    }

    public function getAssistantName(): ?string
    {
        $assistant = $this->getAssistant();

        if (empty($assistant)) {
            return null;
        }

        return $assistant['name'] ?? null;
    }

    public function getAssistant(): ?array
    {
        static $assistant = null;

        if ($assistant !== null) {
            return $assistant;
        }

        return $assistant = $this->getAssistantFromString();
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
        static $avatar = null;

        if (!empty($avatar)) {
            return $avatar;
        }

        if (empty($assistant) || empty($assistant['avatar'])) {
            return null;
        }

        return $avatar = Image::getImageAttachmentData($assistant['avatar'], [150, 150]);
    }

    private function getAssistantFromString(): ?array
    {
        $allAssistants = $this->config->getAssistants();


        if (empty($allAssistants)) {
            return null;
        }

        if (empty($this->assistantName) || $this->assistantName === 'Default') {
            return $this->config->getDefaultAssistant();
        }

        $filteredAssistant = array_filter($allAssistants, fn($a) => $a['name'] === ($this->assistantName ?? null));
        return reset($filteredAssistant) ?: null;
    }
}