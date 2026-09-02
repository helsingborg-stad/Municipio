<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use WpService\Contracts\CurrentTime;

/**
 * Maps the time at which the record is created.
 */
class MapSearchIndexTimestamp implements PropertyMapperInterface
{
    public function __construct(private CurrentTime $wpService)
    {
    }

    /**
     * Map the current site-local timestamp.
     */
    public function mapProperty(\WP_Post $post): string
    {
        return (string) $this->wpService->currentTime('Y-m-d H:i:s');
    }
}