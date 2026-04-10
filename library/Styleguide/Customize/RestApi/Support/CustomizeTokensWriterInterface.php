<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize\RestApi\Support;

use WP_REST_Request;

interface CustomizeTokensWriterInterface
{
    /**
     * Persist raw JSON customization payload for current request context.
     */
    public function write(WP_REST_Request $request, string $encodedTokens): bool;
}
