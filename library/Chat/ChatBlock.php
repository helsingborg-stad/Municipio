<?php

namespace Municipio\Chat;

use Municipio\Chat\Config\ChatConfigInterface;
use Municipio\Chat\Render\ChatRenderConfig;
use Municipio\Chat\Render\ChatRenderInterface;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\__;
use WpService\Contracts\AddAction;
use WpService\Contracts\RegisterBlockType;
use WpService\Contracts\GetBlockWrapperAttributes;

class ChatBlock implements Hookable {
    public function __construct(
        private RegisterBlockType&AddAction&__&GetBlockWrapperAttributes $wpService,
        private ChatConfigInterface $config,
        private ChatRenderInterface $renderer
    ) {}

    public function addHooks(): void
    {
        if (!$this->config->isEnabled() || empty($this->config->getAssistants())) {
            return;
        }

        $this->wpService->addAction('init', [$this, 'registerBlock']);
    }

    public function registerBlock()
    {
        $result = array_column($this->config->getAssistants(), 'name', 'name');
        $result = array_merge(['Default' => __('Default', 'municipio')], $result);

        $this->wpService->registerBlockType(
            'municipio/chat',
            [
                'title' => __('Chat', 'municipio'),
                'description' => __('A simple chat block.', 'municipio'),
                'attributes' => [
                    'assistant' => [
                        'label' => __('Assistant', 'municipio'),
                        'type' => 'string',
                        'enum' => array_keys($result),
                        'default' => 'Default'
                    ]
                ],
                'render_callback' => function ($attributes) {
                    return $this->renderer->render(new ChatRenderConfig(
                        $this->wpService,
                        $this->config,
                        'block',
                        $attributes['assistant'] ?? 'Default',
                        $this->wpService->getBlockWrapperAttributes()
                    ));
                },
                'supports' => array(
                    'autoRegister' => true,
                ),
            ]
        );
    }
}

