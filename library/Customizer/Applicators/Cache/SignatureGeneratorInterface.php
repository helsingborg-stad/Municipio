<?php

namespace Municipio\Customizer\Applicators\Cache;

/**
 * Interface for generating content signatures for cache invalidation
 */
interface SignatureGeneratorInterface
{
    /**
     * Generate a signature for the given data
     *
     * @param array $data The data to generate signature for
     * @return string The generated signature
     */
    public function generateSignature(array $data): string;

    /**
     * Get the customizer fields signature
     *
     * @return string The fields signature
     */
    public function getCustomizerFieldSignature(): string;

    /**
     * Get the last published timestamp
     *
     * @return string The timestamp or 'unknown'
     */
    public function getLastPublishedTimestamp(): string;
}