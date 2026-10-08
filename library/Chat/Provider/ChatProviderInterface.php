<?php

declare(strict_types=1);

namespace Municipio\Chat\Provider;

use Municipio\Chat\PIIRedactor\RedactionResult;

interface ChatProviderInterface
{
    public function validateAssistantConfig(array $assistant): ?\WP_Error;

    public function registerSseStream(
        \WP_REST_Request $request,
        array $assistant,
        array $params,
        RedactionResult $redaction,
    ): void;
}
