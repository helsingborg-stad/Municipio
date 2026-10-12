<?php

namespace Municipio\PostsList\Block\PostsListEventsBlockRender;

use Municipio\SchemaData\Utils\SchemaToPostTypesResolver\SchemaToPostTypeResolverInterface;
use WpService\Contracts\RenderBlock;

class PostsListEventsBlockRender
{
    public function __construct(
        private RenderBlock $wpService,
        private SchemaToPostTypeResolverInterface $postTypeResolver,
    ) {}

    public function render(array $attributes): string
    {
        $postType = $this->postTypeResolver->resolve('Event');

        if (count($postType) < 1) {
            throw new \RuntimeException('No post types found for schema type "Event".');
        }

        $attributes = array_merge([
            'postType' => reset($postType),
            'dateSource' => 'startDate',
            'order' => 'asc',
            'design' => 'schema',
        ], $attributes);

        return $this->wpService->renderBlock([
            'blockName' => 'municipio/posts-list-block',
            'attrs' => $attributes,
            'innerBlocks' => [],
            'innerHTML' => '',
            'innerContent' => [],
        ]);
    }
}
