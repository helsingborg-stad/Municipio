<?php

namespace Municipio\ProgressReporter;

use Municipio\ProgressReporter\HttpHeader\HttpHeaderInterface;

/**
 * Class NullProgressReporterService
 *
 * A null implementation of the ProgressReporterInterface.
 * This class does nothing and is used when no progress reporter is needed.
 */
class NullProgressReporterService implements ProgressReporterInterface
{
    /**
     * @inheritDoc
     */
    public function start(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function setMessage(string $message): void
    {
    }

    /**
     * @inheritDoc
     */
    public function setPercentage(int $percentage): void
    {
    }

    /**
     * @inheritDoc
     */
    public function finish(string $message): void
    {
    }
}
