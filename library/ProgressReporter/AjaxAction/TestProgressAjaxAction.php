<?php

declare(strict_types=1);

namespace Municipio\ProgressReporter\AjaxAction;

/**
 * Configurable progress action used by the shared flow tests.
 */
class TestProgressAjaxAction extends AbstractProgressAjaxAction
{
    public int $executionCount = 0;
    public bool $shouldFail = false;

    /**
     * Get the test action name.
     */
    protected function actionName(): string
    {
        return 'test_progress_action';
    }

    /**
     * Get test request configuration.
     */
    protected function config(): ProgressAjaxActionConfig
    {
        return new ProgressAjaxActionConfig(
            requiredCapability: 'manage_options',
            messages: new ProgressAjaxActionMessages('Unauthorized', 'Invalid method', 'Invalid nonce'),
            requiredMethod: 'POST',
            nonceAction: 'test_progress_action',
        );
    }

    /**
     * Execute the test operation.
     */
    protected function execute(): string
    {
        $this->executionCount++;

        if ($this->shouldFail) {
            throw new \RuntimeException('Sensitive failure');
        }

        return 'Complete';
    }

    /**
     * Hide operation details from the response.
     */
    protected function failureMessage(\Throwable $throwable): string
    {
        return 'Failed';
    }
}