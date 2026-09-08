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