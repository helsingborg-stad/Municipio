<?php

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

/*
 * Apply date from posts list config to get posts args
 */
class ApplyDate implements ApplyPostsListConfigToGetPostsArgsInterface
{
    /**
     * Apply date from posts list config to get posts args
     *
     * @param GetPostsConfigInterface $config
     * @param array $args
     * @return array
     */
    public function apply(GetPostsConfigInterface $config, array $args): array
    {
        return [
            ...$args,
            ...$this->tryGetDateQuery($config)
        ];
    }

    /**
     * Try to get date query from config
     *
     * @param GetPostsConfigInterface $config
     * @return array
     */
    private function tryGetDateQuery(GetPostsConfigInterface $config): array
    {
        $dateFrom = $config->getDateFrom();
        $dateTo   = $config->getDateTo();

        if ($dateFrom || $dateTo) {
            $dateQuery = [];

            if ($dateFrom) {
                $dateQuery['after'] = $dateFrom;
            }

            if ($dateTo) {
                $dateQuery['before'] = $dateTo;
            }

            return [ 'date_query' => $dateQuery ];
        }

        return [];
    }
}
