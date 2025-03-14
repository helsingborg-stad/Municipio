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

    /**
     * Set the progress message.
     *
     * @param string $message The progress message.
     * @return void
     */
    public function setMessage(string $message): void;

    /**
     * Set the progress percentage.
     *
     * @param int $percentage The progress percentage.
     * @return void
     */
    public function setPercentage(int|float $percentage): void;

    /**
     * Finish the progress.
     *
     * @param string $message The completion message.
     * @return void
     */
    public function finish(string $message): void;
}
