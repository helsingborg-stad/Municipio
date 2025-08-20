<?php

namespace Municipio\Content\PostFilters;

use Municipio\SchemaData\Config\Contracts\TryGetSchemaTypeFromPostType;
use Municipio\HooksRegistrar\Hookable;
use WP_Query;
use WpService\Contracts\AddAction;

/**
 * Class RemoveExpiredEventsFromMainArchiveQuery
 */
class RemoveExpiredEventsFromMainArchiveQuery implements Hookable
{
    /**
     * Constructor.
     */
    public function __construct(
        private AddAction $wpService,
        private TryGetSchemaTypeFromPostType $tryGetSchemaTypeFromPostType
    ) {
    }

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
        if (!$query->is_main_query() || !$query->is_archive()) {
            return;
        }

        if ($this->tryGetSchemaTypeFromPostType->tryGetSchemaTypeFromPostType($query->get('post_type')) !== 'Event') {
            return;
        }

        $currentDate = new \DateTime();
        $metaQuery   = $query->get('meta_query') ?: [];

        $metaQuery[] = [
            'key'     => 'endDate',
            'value'   => $currentDate->format('Y-m-d H:i:s'),
            'compare' => '>=',
            'type'    => 'DATETIME',
        ];

        $query->set('meta_query', $metaQuery);
    }
}
