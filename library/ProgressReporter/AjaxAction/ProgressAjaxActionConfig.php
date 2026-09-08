<?php

declare(strict_types=1);

namespace Municipio\ProgressReporter\AjaxAction;

/**
 * Configures validation and response messages for a progress AJAX action.
 */
class ProgressAjaxActionConfig
{
    /**
     * Create progress AJAX action configuration.
     */
    public function __construct(
        public readonly string $action,
        public readonly string $requiredCapability,
        public readonly ProgressAjaxActionMessages $messages,
        public readonly ?string $requiredMethod = null,
        public readonly ?string $nonceAction = null,
    ) {}
}