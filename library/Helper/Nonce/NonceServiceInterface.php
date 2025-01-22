<?php

namespace Municipio\Helper\Nonce;

interface NonceServiceInterface extends
    Contracts\GetNonce,
    Contracts\GetNonceField,
    Contracts\PrintNonceField,
    Contracts\VerifyNonceField
{
}
