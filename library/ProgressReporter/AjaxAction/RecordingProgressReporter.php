<?php

declare(strict_types=1);

namespace Municipio\ProgressReporter\AjaxAction;

use Municipio\ProgressReporter\ProgressReporterInterface;

/**
 * Records completion messages emitted by progress actions in tests.
 */
class RecordingProgressReporter implements ProgressReporterInterface
{
    /** @var array<int, string> */
    public array $messages = [];

    /** Start progress reporting. */
    public function start(): void {}

    /** Ignore intermediate messages. */
    public function setMessage(string $message): void {}

    /** Ignore percentages. */
    public function setPercentage(int|float $percentage): void {}

    /** Record the final message. */
    public function finish(string $message): void
    {
        $this->messages[] = $message;
    }
}