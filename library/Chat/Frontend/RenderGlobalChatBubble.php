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
                'lang' => [
                    'chat' => $this->wpService->__('Chat', 'municipio'),
                    'send' => $this->wpService->__('Send', 'municipio'),
                ],
            ])
        ;
    }
}
