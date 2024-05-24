<?php

namespace Municipio\ExternalContent\Sync;

use Municipio\ExternalContent\Sources\ISource;
use Municipio\ExternalContent\WpPostFactory\WpPostFactoryInterface;
use WP_Post;
use WpService\Contracts\InsertPost;

class SyncSourceToLocal implements ISyncSourceToLocal
{
    public function __construct(private WpPostFactoryInterface $wpPostFactory, private InsertPost $wpService)
    {
    }

    public function sync(ISource $source): void
    {
        $posts = array_map([$this->wpPostFactory, 'create'], $source->getObjects());
        $posts = array_map(fn($post) => $this->setPostTypeFromSourceAndReturnPost($source, $post), $posts);
        array_map([$this->wpService, 'insertPost'], $posts);
    }

    private function setPostTypeFromSourceAndReturnPost(ISource $source, WP_Post $post): WP_Post
    {
        $post->post_type = $source->getPostType();
        return $post;
    }
}
