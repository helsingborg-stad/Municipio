<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize\RestApi\Support;

use WP_REST_Request;

interface CustomizeTokensReaderInterface
{
    /**
     * Read raw JSON customization payload for current request context.
     */
    public function read(WP_REST_Request $request): ?string;
}
