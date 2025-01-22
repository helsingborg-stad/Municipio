<?php

namespace Municipio\Helper\Nonce\Contracts;

interface PrintNonceField
{
    /**
     * Prints a nonce field.
     *
     * @param string|null $action The nonce action name. If not provided, a default action name will be used.
     * @param string|null $name The nonce field name. If not provided, a default name will be used.
     * @return void
     */
    public function printNonceField(?string $action = null, ?string $name = null): void;
}
