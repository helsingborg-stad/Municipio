<?php

namespace Municipio\Api\Posts;

use Municipio\HooksRegistrar\Hookable;
use WP_REST_Request;
use WpService\Contracts\AddFilter;

/**
 * Enable post types param.
 */
class EnablePostTypesParam implements Hookable
{
    /**
     * Constructor.
     */
    public function __construct(private AddFilter $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('rest_post_query', [$this, 'enablePostTypesParam'], 10, 2);
    }

    /**
     * Enable post types param.
     *
     * @param array $args
     * @param WP_REST_Request $request
     * @return array
     */
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
