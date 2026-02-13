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
        // Prevent this block's script from loading in Customizer to avoid React errors
        $this->wpService->addAction('customize_controls_enqueue_scripts', [$this, 'excludeFromCustomizer'], 1);
    }

    private function getBlockJsonPath(): string
    {
        return __DIR__ . '/block.json';
    }

    public function registerBlock(): void
    {
        $this->wpService->registerBlockType($this->getBlockJsonPath());
    }

    /**
     * Exclude this block's editor script from Customizer to prevent React dependency errors
     */
    public function excludeFromCustomizer(): void
    {
        $scriptHandle = $this->getEditorScriptHandle();
        $this->wpService->wpDequeueScript($scriptHandle);
        $this->wpService->wpDeregisterScript($scriptHandle);
    }

    private function getEditorScriptHandle(): string
    {
        // The handle is derived from the block name in block.json, which is "municipio/posts-list"
        $blockJson = json_decode(file_get_contents($this->getBlockJsonPath()), true);
        $blockName = $blockJson['name'] ?? 'municipio/posts-list';
        $handle = str_replace('/', '-', $blockName) . '-editor-script';
        return $handle;
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
