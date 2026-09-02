<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use WpService\Contracts\GetPostTaxonomies;
use WpService\Contracts\WpGetPostTerms;

/**
 * Maps terms from non-category taxonomies as tags.
 */
class MapTags implements PropertyMapperInterface
{
    public function __construct(private GetPostTaxonomies&WpGetPostTerms $wpService)
    {
    }

    /**
     * Map all valid non-category term names.
     *
     * @return array<int, string>
     */
    public function mapProperty(\WP_Post $post): array
    {
        $tags = [];

        foreach ($this->wpService->getPostTaxonomies($post) as $taxonomy) {
            if ($taxonomy === 'category') {
                continue;
            }

            $terms = $this->wpService->wpGetPostTerms($post->ID, $taxonomy);
            if (!$terms instanceof \WP_Error) {
                $tags = [
                    ...$tags,
                    ...array_map(static fn(\WP_Term $term): string => $term->name, $terms),
                ];
            }
        }

        return $tags;
    }
}