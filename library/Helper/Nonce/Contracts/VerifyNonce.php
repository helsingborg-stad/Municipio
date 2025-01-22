<?php

namespace Municipio\Helper\Nonce\Contracts;

interface VerifyNonceField
{
    /**
     * Verify a nonce.
     *
     * @param string $nonce The nonce value.
     * @param string|null $action The nonce action name. If not provided, a default action name will be used.
     * @return bool
     */
    public function verifyNonceField(string $nonce, ?string $action = null): bool;
}
