<?php

namespace Municipio\ProgressReporter;

use Municipio\ProgressReporter\HttpHeader\HttpHeaderInterface;

/**
 * Class SseProgressReporterService
 */
class SseProgressReporterService implements ProgressReporterInterface
{
    /**
     * SseProgressReporterService constructor.
     *
     * @param HttpHeaderInterface $httpHeader
     */
    public function __construct(private HttpHeaderInterface $httpHeader)
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
    }

    /**
     * @inheritDoc
     */
    public function setMessage(string $message): void
    {
        ob_flush();
        flush();
        echo "event: message\n";
        echo "data: $message\n\n";
    }

    /**
     * @inheritDoc
     */
    public function setPercentage(int $percentage): void
    {
        ob_flush();
        flush();
        echo "event: progress\n";
        echo "data: $percentage\n\n";
    }

    /**
     * @inheritDoc
     */
    public function finish(string $message): void
    {
        ob_flush();
        flush();
        echo "event: finish\n";
        echo "data: $message\n\n";
        exit();
    }
}
