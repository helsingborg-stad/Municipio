<?php

namespace Municipio\PostsList;

use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\Config\PostsListConfigInterface;
use Municipio\PostsList\GetPosts\GetPostsFromPostsListConfig;
use WpService\Contracts\GetPosts;

class PostList
{
    public function __construct(private PostsListConfigInterface $config, private GetPosts $wpService)
    {
    }

    public function getTemplateDir(): string
    {
        return __DIR__ . '/views/';
    }

    /**
     * Get data for rendering
     *
     * @return array
     */
    public function getData(): array
    {
        return ['posts' => $this->getPosts(), 'config' => $this->getConfig()];
    }

    /**
     * Get posts based on config
     *
     * @return PostObjectInterface[]
     */
    private function getPosts(): array
    {
        return (new GetPostsFromPostsListConfig($this->config, $this->wpService))->getPosts();
    }

    private function getConfig(): PostsListConfigInterface
    {
        $anyPostHasImage = new AnyPostHasImage($this->getPosts());
        var_dump($anyPostHasImage->check());
        die();

        return $this->config;
    }
}
