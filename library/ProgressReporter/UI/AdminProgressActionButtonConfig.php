<?php

declare(strict_types=1);

namespace Municipio\ProgressReporter\UI;

/**
 * Contains feature-specific input for an admin progress action button.
 */
class AdminProgressActionButtonConfig
{
    /**
     * Create admin progress action button configuration.
     *
     * @param array<string, scalar> $parameters
     */
    public function __construct(
        public readonly string $action,
        public readonly string $label,
        public readonly array $parameters = [],
        public readonly AdminProgressActionButtonState $state = AdminProgressActionButtonState::Enabled,
        public readonly ?string $errorMessage = null,
    ) {}
}