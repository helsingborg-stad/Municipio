<?php

namespace Modularity\Module\Posts\Helper\GetPosts;

class PostsResult implements PostsResultInterface
{

    public function __construct(private array $posts, private int $numberOfPages, private array $stickyPosts)
    {
        foreach ($this->posts as $post) {
            if (!$post instanceof \WP_Post) {
                throw new \InvalidArgumentException('Posts must be an array of WP_Post objects.');
            }
        }

        foreach ($this->stickyPosts as $stickyPost) {
            if (!$stickyPost instanceof \WP_Post) {
                throw new \InvalidArgumentException('Sticky posts must be an array of WP_Post objects.');
            }
        }
    }

    public function getPosts(): array
    {
        return $this->posts;
    }

    public function getNumberOfPages(): int
    {
        return $this->numberOfPages;
    }

    public function getStickyPosts(): array
    {
        return $this->stickyPosts;
    }
}