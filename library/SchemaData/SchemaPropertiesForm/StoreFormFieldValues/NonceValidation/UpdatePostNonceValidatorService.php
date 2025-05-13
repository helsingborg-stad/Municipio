<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\NonceValidation;

use WpService\Contracts\WpVerifyNonce;

class UpdatePostNonceValidatorService implements PostNonceValidatorInterface
{
    public function __construct(private WpVerifyNonce $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function isValid(int $postId, ?string $nonce): bool
    {
        return $this->wpService->wpVerifyNonce($nonce, "update-post_{$postId}") !== false;
    }
}
