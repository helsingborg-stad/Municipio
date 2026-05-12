<?php

namespace Municipio\Chat;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\RegisterBlockType;

class ChatBlock implements Hookable {
    public function __construct(private RegisterBlockType&AddAction $wpService)
    {}

    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'registerBlock']);
    }

    public function registerBlock()
    {
        $this->wpService->registerBlockType(
            'municipio/chat',
            [
                'title' => __('Chat', 'municipio'),
                'description' => __('A simple chat block.', 'municipio'),
                'attributes' => [
                    'assistant' => [
                        'label' => __('Assistant', 'municipio'),
                        'type' => 'string',
                        'default' => 'gpt-3.5-turbo'
                    ]
                ],
                'render_callback' => function ($attributes) {
                    return sprintf(
                        __( '<p>%s: Assistant</p>', 'municipio' ),
                        esc_html( $attributes['assistant'] )
                    );
                },
                'supports' => array(
                    'autoRegister' => true,
                ),
            ]
        );
    }
}

