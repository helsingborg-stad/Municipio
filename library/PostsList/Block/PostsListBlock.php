<?php

namespace Municipio\PostsList\Block;

use Municipio\Helper\Post;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\RegisterBlockType;

class PostsListBlock implements Hookable
{
    public function __construct(
        private AddAction&RegisterBlockType $wpService,
        private BlockRendererInterface $blockRenderer,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'registerBlock']);
        // add custom rest api endpoint
        $this->wpService->addAction('rest_api_init', [$this, 'registerRestEndpoint']);
    }

    public function registerBlock(): void
    {
        $this->wpService->registerBlockType(__DIR__ . '/block.json', [
            'render_callback' => [$this->blockRenderer, 'render'],
        ]);
    }

    public function registerRestEndpoint(): void
    {
        register_rest_route('municipio/v1', '/meta-keys/(?P<postType>[a-zA-Z0-9_-]+)', [
            'methods' => 'GET',
            'callback' => [$this, 'getPostTypeMetaKeys'],
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            },
        ]);
    }

    public function getPostTypeMetaKeys(\WP_REST_Request $request): array
    {
        $postType = $request->get_param('postType');

        return Post::getPosttypeMetaKeys($postType);
    }
}
