<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use WpService\Contracts\ApplyFilters;

/**
 * Maps searchable post content.
 */
class MapContent implements PropertyMapperInterface
{
    use SanitizesText;

    public function __construct(private ApplyFilters $wpService)
    {
    }

    /**
     * Apply content filters and normalize the resulting text.
     */
    public function mapProperty(\WP_Post $post): string
    {
        $content = (string) $this->wpService->applyFilters('the_content', $post->post_content);
        $content = $this->wpService->applyFilters('Municipio/SearchIndex/Record/Content', $content, $post->ID);

        return is_string($content) ? $this->sanitizeText($content) : '';
    }
}