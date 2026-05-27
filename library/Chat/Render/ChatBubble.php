<?php

namespace Municipio\Chat\Render;

use Municipio\Chat\Config\ChatConfigInterface;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\__;
use WpService\Contracts\AddAction;

class ChatBubble implements Hookable
{
    public function __construct(
        private __&AddAction $wpService,
        private ChatConfigInterface $config,
        private ChatRenderInterface $renderer
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
        $config = new ChatRenderConfig(
            $this->wpService,
            $this->config,
            'fab',
            'Default'
        );

        echo $this->renderer->render($config);
    }
}
