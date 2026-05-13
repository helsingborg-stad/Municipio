<?php

namespace Municipio\Chat\Frontend;

use ComponentLibrary\Renderer\RendererInterface;
use Municipio\Chat\Config\ChatConfigInterface;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\__;
use WpService\Contracts\AddAction;
use Municipio\Helper\Image;

class RenderGlobalChatBubble implements Hookable
{
    public function __construct(
        private __&AddAction $wpService,
        private RendererInterface $renderer,
        private ChatConfigInterface $config,
    ) {}

    public function addHooks(): void
    {
        if (!$this->config->isEnabled() || !$this->config->isGlobalChatEnabled() || empty($this->config->getDefaultAssistant())) {
            return;
        }

        $this->wpService->addAction('wp_footer', [$this, 'render']);
    }

    public function render(): void
    {
        $defaultAssistant = $this->config->getDefaultAssistant();
        $avatar = !empty($defaultAssistant['avatar']) ? Image::getImageAttachmentData($defaultAssistant['avatar'], [150, 150]) : null;

        $attributeList = [
            'style' => '--c-chat--inner-padding-multiplier: 2;',
        ];

        if (!empty($defaultAssistant['greetings_phrase'])) {
            $attributeList['data-js-chat-greetings-phrase'] = $defaultAssistant['greetings_phrase'];
        }

        echo
            $this->renderer->render('ChatBubble', [
                'lang' => [
                    'chat' => $this->wpService->__('Chat', 'municipio'),
                    'close' => $this->wpService->__('Close', 'municipio'),
                    'send' => $this->wpService->__('Send', 'municipio'),
                    'placeholder' => $this->wpService->__('Write your question here', 'municipio'),
                    'newConversation' => $this->wpService->__('New conversation', 'municipio'),
                ],
                'avatar' => $avatar,
                'attributeList' => $attributeList,
                'name' => $defaultAssistant['name'] ?? null,
            ])
        ;
    }
}
