<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use WpService\Contracts\WpGetPostTerms;

/**
 * Maps category term names.
 */
class MapCategories implements PropertyMapperInterface
{
    public function __construct(private WpGetPostTerms $wpService)
    {
    }

    /**
     * Map category names, ignoring taxonomy errors.
     *
     * @return array<int, string>
     */
    public function mapProperty(\WP_Post $post): array
    {
        $terms = $this->wpService->wpGetPostTerms($post->ID, 'category');

        return $terms instanceof \WP_Error
            ? []
            : array_map(static fn(\WP_Term $term): string => $term->name, $terms);
    }
}