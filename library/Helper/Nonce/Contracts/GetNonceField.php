<?php

namespace Municipio\Helper\Nonce\Contracts;

interface GetNonceField
{
    /**
     * Generate a nonce field.
     *
     * @param string|null $action The nonce action name. If not provided, a default action name will be used.
     * @param string|null $name The nonce field name. If not provided, a default name will be used.
     * @return string The nonce field HTML markup.
     */
    public function getNonceField(?string $action = null, ?string $name = null): string;
}
