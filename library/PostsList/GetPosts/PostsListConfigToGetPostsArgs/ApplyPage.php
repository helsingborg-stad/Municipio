<?php

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

/*
 * Apply page from posts list config to get posts args
 */
class ApplyPage implements ApplyPostsListConfigToGetPostsArgsInterface
{
    /**
     * Apply page from posts list config to get posts args
     *
     * @param GetPostsConfigInterface $config
     * @param array $args
     * @return array
     */
    public function apply(GetPostsConfigInterface $config, array $args): array
    {
        return [ ...$args, 'paged' => $this->sanitizePageNumber($config->getPage()) ];
    }

    /**
     * Sanitize page number to ensure it's at least 1
     *
     * @param int $page
     * @return int
     */
    private function sanitizePageNumber(int $page): int
    {
        return $page < 1 ? 1 : $page;
    }
}
