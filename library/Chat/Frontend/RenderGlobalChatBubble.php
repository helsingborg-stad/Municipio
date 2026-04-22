<?php

namespace Municipio\Chat\Frontend;

use ComponentLibrary\Renderer\RendererInterface;
use Municipio\Chat\Config\ChatConfigInterface;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\__;
use WpService\Contracts\AddAction;

class RenderGlobalChatBubble implements Hookable
{
    public function __construct(
        private __&AddAction $wpService,
        private RendererInterface $renderer,
        private ChatConfigInterface $config,
    ) {}

    public function addHooks(): void
    {
        if (!$this->config->isEnabled() || !$this->config->isGlobalChatEnabled()) {
            return;
        }

        $this->wpService->addAction('wp_footer', [$this, 'render']);
    }

    public function render(): void
    {
        echo
            $this->renderer->render('ChatBubble', [
                'i18n' => [
                    'chat' => $this->wpService->__('Chat', 'municipio'),
                    'chatWithAi' => $this->wpService->__('Chat with AI', 'municipio'),
                    'you' => $this->wpService->__('You', 'municipio'),
                    'assistant' => $this->wpService->__('Assistant', 'municipio'),
                    'writeQuestion' => $this->wpService->__('Write your question here', 'municipio'),
                    'send' => $this->wpService->__('Send', 'municipio'),
                ],
            ])
        ;
    }
}
