<?php

namespace Modularity\Module\Posts\Helper\GetPosts\Schema;

use WpService\Contracts\AddFilter;

class SchemaDataHandler
{
    public function __construct(private AddFilter $wpService, private array $fields)
    {
        if ($this->fields['posts_data_schema_type'] === 'Event') {
            $this->maybeHandleEventPostArgs();
        }
    }

    private function maybeHandleEventPostArgs(): void
    {
        $this->wpService->addFilter('Modularity/Module/Posts/GetPosts/Args', function (array $args) {
            $args['orderby'] = 'meta_value';
            $args['meta_query'] = [
                'event_start_date_clause' => [
                    'key' => 'startDate',
                    'value' => (new \DateTime())->format('Y-m-d H:i:s'),
                    'compare' => '>=',
                    'type'    => 'DATETIME',
                ]
            ];

            return $args;
        });
    }
}