<?php

namespace Municipio\PostsList\Block;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\RegisterBlockType;

class PostsListBlock implements Hookable
{
    public function __construct(private AddAction&RegisterBlockType $wpService, private BlockRendererInterface $blockRenderer)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'registerBlock']);
    }

    public function registerBlock(): void
    {
        $this->wpService->registerBlockType(__DIR__ . '/block.json', [
            'render_callback' => [$this->blockRenderer, 'render']
        ]);
    }
}
