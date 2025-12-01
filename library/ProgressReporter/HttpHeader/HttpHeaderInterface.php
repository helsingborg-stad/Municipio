<?php

namespace Municipio\ProgressReporter\HttpHeader;

/**
 * Interface HttpHeaderInterface
 * A wrapper for the PHP header() function.
 */
interface HttpHeaderInterface
{
    /**
     * Send a raw HTTP header.
     *
     * @param string $header
     * @param bool $replace
     * @param int $httpResponseCode
     * @return HttpHeaderInterface
     */
    public function sendHeader(string $header, bool $replace = true, int $httpResponseCode = 0): HttpHeaderInterface;
}
