<?php

declare(strict_types=1);

namespace Municipio\ProgressReporter\AjaxAction;

/**
 * Contains validation messages for a progress AJAX action.
 */
class ProgressAjaxActionMessages
{
    /**
     * Create progress AJAX action messages.
     */
    public function __construct(
        public readonly string $unauthorized,
        public readonly string $invalidMethod = '',
        public readonly string $invalidNonce = '',
    ) {}
}