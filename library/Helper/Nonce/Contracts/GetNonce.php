<?php

namespace Municipio\Helper\Nonce\Contracts;

interface GetNonce
{
    /**
     * Generate a nonce.
     *
     * @param string|null $action The nonce action name. If not provided, a default action name will be used.
     * @return string The nonce.
     */
    public function getNonce(?string $action = null): string;
}
