<?php

namespace Municipio\PostsList\Block;

use Municipio\Helper\Post;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\CurrentUserCan;
use WpService\Contracts\RegisterBlockType;
use WpService\Contracts\RegisterRestRoute;
use WpService\Contracts\WpDequeueScript;
use WpService\Contracts\WpDeregisterScript;

class PostsListBlock implements Hookable
{
    public function __construct(
        private AddAction&RegisterBlockType&RegisterRestRoute&CurrentUserCan&WpDequeueScript&WpDeregisterScript $wpService,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'registerBlock']);
        $this->wpService->addAction('rest_api_init', [$this, 'registerRestEndpoint']);
    }

    private function getBlockJsonPath(): string
    {
        return __DIR__ . '/block.json';
    }

    public function registerBlock(): void
    {
        $this->wpService->registerBlockType($this->getBlockJsonPath());
    }

    public function registerRestEndpoint(): void
    {
        $this->wpService->registerRestRoute('municipio/v1', '/meta-keys/(?P<postType>[a-zA-Z0-9_-]+)', [
            'methods' => 'GET',
            'callback' => [$this, 'getPostTypeMetaKeys'],
            'permission_callback' => function () {
                return $this->wpService->currentUserCan('edit_posts');
            },
        ]);
    }

    public function getPostTypeMetaKeys(\WP_REST_Request $request): array
    {
        $postType = $request->get_param('postType');

        return Post::getPosttypeMetaKeys($postType);
    }
}
