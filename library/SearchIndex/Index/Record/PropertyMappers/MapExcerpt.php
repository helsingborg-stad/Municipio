<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use WpService\Contracts\GetTheExcerpt;
use WpService\Contracts\WpTrimWords;

/**
 * Maps a post excerpt to normalized searchable text.
 */
class MapExcerpt implements PropertyMapperInterface
{
    use SanitizesText;

    public function __construct(private GetTheExcerpt&WpTrimWords $wpService)
    {
    }

    /**
     * Map the post excerpt, falling back to post content.
     */
    public function mapProperty(\WP_Post $post): string
    {
        $excerpt = $this->wpService->getTheExcerpt($post);
        $excerpt = is_string($excerpt) && $excerpt !== '' ? $excerpt : $post->post_content;
        $excerpt = preg_replace('/\[(.*?)\]/', '', $excerpt) ?? '';

        return $this->wpService->wpTrimWords($this->sanitizeText($excerpt), 55, '...');
    }
}