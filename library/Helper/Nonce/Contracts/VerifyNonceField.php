<?php

namespace Municipio\Helper\Nonce\Contracts;

interface VerifyNonceField
{
    /**
     * Verify the nonce field.
     *
     * @param string|null $nonceField The nonce field name. If not provided, a default name will be used.
     * @param string|null $action The nonce action name. If not provided, a default action name will be used.
     * @return bool
     */
    public function verifyNonceField(?string $nonceField = null, ?string $action = null): bool;
}
