<?php

namespace Municipio\ProgressReporter;

use Municipio\ProgressReporter\HttpHeader\HttpHeaderInterface;
use Municipio\ProgressReporter\OutputBufferFlush\OutputBufferFlushInterface;

/**
 * Class SseProgressReporterService
 */
class SseProgressReporterService implements ProgressReporterInterface
{
    /**
     * SseProgressReporterService constructor.
     *
     * @param HttpHeaderInterface $httpHeader
     * @param OutputBufferFlushInterface $ob
     */
    public function __construct(private HttpHeaderInterface $httpHeader, private OutputBufferFlushInterface $ob)
    {
    }

    /**
     * @inheritDoc
     */
    public function start(): void
    {
        $this->httpHeader->sendHeader("Content-Type: text/event-stream");
        $this->httpHeader->sendHeader("X-Accel-Buffering: no");
        $this->httpHeader->sendHeader("Cache-Control: no-cache");

        echo "event: start\n";

        // Register shutdown function to handle fatal errors
        register_shutdown_function([$this, 'handleShutdown']);
    }

    /**
     * @inheritDoc
     */
    public function setMessage(string $message): void
    {
        echo "event: message\n";
        echo "data: $message\n\n";
        $this->ob->flush();
    }

    /**
     * @inheritDoc
     */
    public function setPercentage(int|float $percentage): void
    {
        $percentage = (int) $percentage;

        echo "event: progress\n";
        echo "data: $percentage\n\n";

        $this->ob->flush();
    }

    /**
     * Handle script shutdown and send error event if a fatal error occurred.
     */
    private function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            echo "event: error\n";
            echo "data: {$error['message']}\n\n";
            $this->ob->flush();
        }
    }

    /**
     * @inheritDoc
     */
    public function finish(string $message): void
    {
        echo "event: finish\n";
        echo "data: $message\n\n";
        $this->ob->flush();
        exit();
    }
}
