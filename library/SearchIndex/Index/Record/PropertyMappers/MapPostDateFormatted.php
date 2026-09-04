<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use WpService\Contracts\GetOption;

/**
 * Maps the formatted post publication date.
 */
class MapPostDateFormatted implements PropertyMapperInterface
{
    public function __construct(private GetOption $wpService)
    {
    }

    /**
     * Format the publication date using the configured WordPress format.
     */
    public function mapProperty(\WP_Post $post): string
    {
        return date(
            (string) $this->wpService->getOption('date_format'),
            (int) strtotime($post->post_date),
        );
    }
}