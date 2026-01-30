<?php

namespace Municipio\Content\PostFilters;

use Municipio\HooksRegistrar\Hookable;
use Municipio\SchemaData\Config\Contracts\TryGetSchemaTypeFromPostType;
use WP_Query;
use WpService\Contracts\AddAction;
use WpService\Contracts\IsAdmin;
use WpService\Contracts\IsArchive;

/**
 * Class RemoveExpiredEventsFromMainArchiveQuery
 */
class RemoveExpiredEventsFromMainArchiveQuery implements Hookable
{
    /**
     * Constructor.
     */
    public function __construct(
        private AddAction&IsAdmin&IsArchive $wpService,
        private TryGetSchemaTypeFromPostType $tryGetSchemaTypeFromPostType,
    ) {}

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('pre_get_posts', [$this, 'removeExpiredEventsFromMainArchiveQuery']);
    }

    /**
     * Remove expired events from the main archive query.
     *
     * @param WP_Query $query
     * @return void
     */
    public function removeExpiredEventsFromMainArchiveQuery(WP_Query &$query): void
    {
        if ($this->wpService->isAdmin() || !$this->wpService->isArchive()) {
            return;
        }

        # If its a query for more than one post type, we can't determine if we should filter out expired events
        if (is_array($query->get('post_type')) && count($query->get('post_type')) > 1) {
            return;
        }

        # If it was an array with only one post type, we can extract it
        if (is_array($query->get('post_type')) && count($query->get('post_type')) === 1) {
            $query->set('post_type', $query->get('post_type')[0]);
        }

        if ( $this->tryGetSchemaTypeFromPostType->tryGetSchemaTypeFromPostType($query->get('post_type')) !== 'Event') {
            return;
        }
        
        $currentDate = new \DateTime();
        $metaQuery = $query->get('meta_query') ?: [];

        $metaQuery[] = [
            'key' => 'startDate',
            'value' => $currentDate->format('Y-m-d H:i:s'),
            'compare' => '>=',
            'type' => 'DATETIME',
        ];

        $query->set('meta_query', $metaQuery);
    }
}
