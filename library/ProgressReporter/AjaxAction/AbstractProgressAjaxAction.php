<?php

declare(strict_types=1);

namespace Municipio\ProgressReporter\AjaxAction;

use Municipio\HooksRegistrar\Hookable;
use Municipio\ProgressReporter\ProgressReporterInterface;
use WpService\Contracts\AddAction;
use WpService\Contracts\CheckAjaxReferer;
use WpService\Contracts\CurrentUserCan;

/**
 * Handles shared validation and reporting for admin progress actions.
 */
abstract class AbstractProgressAjaxAction implements Hookable
{
    /**
     * Create a progress AJAX action.
     */
    public function __construct(
        protected AddAction&CheckAjaxReferer&CurrentUserCan $wpService,
        protected ProgressReporterInterface $progressReporter,
        private ProgressAjaxActionConfig $config,
    ) {}

    /**
     * Register the authenticated AJAX action.
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('wp_ajax_' . $this->config->action, [$this, 'handleRequest']);
    }

    /**
     * Validate and execute the progress action.
     */
    public function handleRequest(): void
    {
        $this->progressReporter->start();

        if (!$this->wpService->currentUserCan($this->config->requiredCapability)) {
            $this->progressReporter->finish($this->config->messages->unauthorized);
            return;
        }

        $requiredMethod = $this->config->requiredMethod;
        if ($requiredMethod !== null && ($_SERVER['REQUEST_METHOD'] ?? '') !== $requiredMethod) {
            $this->progressReporter->finish($this->config->messages->invalidMethod);
            return;
        }

        $nonceAction = $this->config->nonceAction;
        if ($nonceAction !== null && $this->wpService->checkAjaxReferer($nonceAction, false, false) === false) {
            $this->progressReporter->finish($this->config->messages->invalidNonce);
            return;
        }

        set_time_limit(0);

        try {
            $message = $this->execute();
        } catch (\Throwable $throwable) {
            $message = $this->failureMessage($throwable);
        }

        $this->progressReporter->finish($message);
    }

    /**
     * Execute the feature operation and return its completion message.
     */
    abstract protected function execute(): string;

    /**
     * Convert an operation failure to a safe completion message.
     */
    protected function failureMessage(\Throwable $throwable): string
    {
        return $throwable->getMessage();
    }
}