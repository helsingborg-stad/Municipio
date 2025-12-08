<?php

namespace Municipio\PostsList\Block\PostsListBlockRenderer;

interface BlockRendererInterface
{
    public function render(array $attributes, string $content, \WP_Block $block): string;
}
