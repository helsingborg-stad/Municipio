<?php

namespace Municipio\Api\Posts;

use Municipio\HooksRegistrar\Hookable;
use WP_REST_Request;
use WpService\Contracts\AddFilter;

class EnablePostTypesParam implements Hookable
{
    public function __construct(private AddFilter $wpService)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter('rest_post_query', [$this, 'enablePostTypesParam'], 10, 2);
    }

    public function enablePostTypesParam(array $args, WP_REST_Request $request): array
    {
        if (empty($request['post_type'])) {
            return $args;
        }

        if (is_string($request['post_type'])) {
            $args['post_type'] = explode(',', $request['post_type']);
        } elseif (is_array($request['post_type'])) {
            $args['post_type'] = $request['post_type'];
        }

        return $args;
    }
}
