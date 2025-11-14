<?php

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

/*
 * Apply tax query from posts list config to get posts args
 */
class ApplyTaxQuery implements ApplyPostsListConfigToGetPostsArgsInterface
{
    /**
     * Constructor
     *
     * @param \WP_Taxonomy[] $wpTaxonomies
     */
    public function __construct(private array $wpTaxonomies)
    {
    }

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

    /**
     * Try to get tax query from posts list config
     *
     * @param GetPostsConfigInterface $config
     * @return array
     */
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
        $taxQuery = [ 'relation' => $this->getRelationFromConfig($config) ];
        foreach ($termsByTaxonomy as $taxonomy => $termIds) {
            $taxonomyIsHierarchical = $this->taxonomyIsHierarchical($taxonomy);

            $taxQuery[] = [
                'taxonomy' => $taxonomy,
                'field'    => 'term_id',
                'terms'    => $termIds,
                'operator' => $taxonomyIsHierarchical ? 'IN' : 'AND'
            ];
        }

        return ['tax_query' => $taxQuery];
    }

    /**
     * Get relation from config
     *
     * @param GetPostsConfigInterface $config
     * @return string
     */
    private function getRelationFromConfig(GetPostsConfigInterface $config): string
    {
        return  $config->isFacettingTaxonomyQueryEnabled() ? 'AND' : 'OR';
    }

    private function taxonomyIsHierarchical(string $taxonomy): bool
    {
        foreach ($this->wpTaxonomies as $wpTaxonomy) {
            if ($wpTaxonomy->name === $taxonomy) {
                return $wpTaxonomy->hierarchical;
            }
        }

        return false;
    }
}
