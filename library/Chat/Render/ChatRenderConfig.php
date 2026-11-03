<?php

namespace Municipio\Chat\Render;

use WpService\Contracts\__;

class ChatRenderConfig implements ChatRenderConfigInterface
{
    private ?string $id = null;
    public function __construct(
        private __ $wpService,
        private string $view,
        private array $assistants,
        private ?string $wrapperAttributes = '',
    ) {}

    public function getView(): string
    {
        return $this->view;
    }

    public function getChatId(): string
    {
        return $this->id ??= uniqid('chat-', true);
    }

    public function getWrapperAttributes(): ?string
    {
        return $this->wrapperAttributes;
    }

    public function getAttributeList(): array
    {
        $attributeList = [
            'data-js-chat-assistants' => $this->getAssistantsIntegrationTypesAsJson(),
        ];

        $attributeList['data-js-municipio-chat'] = '1';

        return $attributeList;
    }

    public function getAssistants(): array {
        return $this->assistants;
    }

    private function getAssistantsIntegrationTypesAsJson(): string
    {
        $assistantIntegrationTypes = [];

        foreach ($this->getAssistants() as $assistant) {
            if (!$this->isValidAssistantRow($assistant)) {
                continue;
            }

            $assistantIntegrationTypes[strtolower($assistant['name'])] = $assistant['acf_fc_layout'];
        }

        return json_encode($assistantIntegrationTypes) ?: '{}';
    }

    private function isValidAssistantRow(mixed $assistant): bool
    {
        return is_array($assistant) && !empty($assistant['name']) && !empty($assistant['acf_fc_layout']);
    }

    public function getLang(): array
    {
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
}
