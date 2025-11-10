<?php

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

/*
 * Apply tax query from posts list config to get posts args
 */
class ApplyTaxQuery implements ApplyPostsListConfigToGetPostsArgsInterface
{
    /**
     * Apply tax query from posts list config to get posts args
     *
     * @param GetPostsConfigInterface $config
     * @param array $args
     * @return array
     */
    public function apply(GetPostsConfigInterface $config, array $args): array
    {
        return [...$args, ...$this->tryGetTaxQueryFromConfig($config)];
    }

    private function tryGetTaxQueryFromConfig(GetPostsConfigInterface $config): array
    {
        $terms = $config->getTerms();

        if (empty($terms)) {
            return [];
        }

        // Group terms by taxonomy
        $termsByTaxonomy = [];
        foreach ($terms as $term) {
            $termsByTaxonomy[$term->taxonomy][] = $term->term_id;
        }

        // Build tax_query array
        $taxQuery = [];
        foreach ($termsByTaxonomy as $taxonomy => $termIds) {
            $taxQuery[] = [
                'taxonomy' => $taxonomy,
                'field'    => 'term_id',
                'terms'    => $termIds,
            ];
        }

        return ['tax_query' => $taxQuery];
    }
}
