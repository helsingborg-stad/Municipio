<?php

declare(strict_types=1);

namespace Municipio\Api\Customize\Support;

use WP_REST_Request;

interface ChangesetIdResolverInterface
{
    /**
     * Resolve customize_changeset post ID from request/query context.
     */
    public function resolve(WP_REST_Request $request): ?int;
}
