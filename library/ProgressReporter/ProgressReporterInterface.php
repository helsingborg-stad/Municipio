<?php

namespace Municipio\ProgressReporter;

interface ProgressReporterInterface
{
    /**
     * Start the progress.
     *
     * @return void
     */
    public function start(): void;

    public function setMessage(string $message): void;

    public function setPercentage(int $percentage): void;

    /**
     * Finish the progress.
     *
     * @return void
     */
    public function finish(string $message): void;
}
