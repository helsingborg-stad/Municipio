<?php

namespace Municipio\ProgressReporter\HttpHeader;

/**
 * Class HttpHeader
 * A wrapper for the PHP header() function.
 */
class HttpHeader implements HttpHeaderInterface
{
    /**
     * @inheritDoc
     */
    public function sendHeader(string $header, bool $replace = true, int $httpResponseCode = 0): HttpHeaderInterface
    {
        header($header, $replace, $httpResponseCode);
        return $this;
    }
}
