<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\NonceValidation;

interface PostNonceValidatorInterface
{
    /**
     * Validates the nonce for a given post ID.
     *
     * @param int $postId The post ID to validate the nonce for.
     * @param string|null $nonce The nonce to validate.
     *
     * @return bool True if the nonce is valid, false otherwise.
     */
    public function isValid(int $postId, ?string $nonce): bool;
}
