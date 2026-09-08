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
    ) {}

    /**
     * Register the authenticated AJAX action.
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('wp_ajax_' . $this->actionName(), [$this, 'handleRequest']);
    }

    /**
     * Validate and execute the progress action.
     */
    public function handleRequest(): void
    {
        $this->progressReporter->start();
        $config = $this->config();

        if (!$this->wpService->currentUserCan($config->requiredCapability)) {
            $this->progressReporter->finish($config->messages->unauthorized);
            return;
        }

        $requiredMethod = $config->requiredMethod;
        if ($requiredMethod !== null && ($_SERVER['REQUEST_METHOD'] ?? '') !== $requiredMethod) {
            $this->progressReporter->finish($config->messages->invalidMethod);
            return;
        }

        $nonceAction = $config->nonceAction;
        if ($nonceAction !== null && $this->wpService->checkAjaxReferer($nonceAction, false, false) === false) {
            $this->progressReporter->finish($config->messages->invalidNonce);
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
     * Get the WordPress AJAX action name.
     */
    abstract protected function actionName(): string;

    /**
     * Get validation and response configuration for the action.
     */
    abstract protected function config(): ProgressAjaxActionConfig;

    /**
     * Convert an operation failure to a safe completion message.
     */
    protected function failureMessage(\Throwable $throwable): string
    {
        return $throwable->getMessage();
    }
}