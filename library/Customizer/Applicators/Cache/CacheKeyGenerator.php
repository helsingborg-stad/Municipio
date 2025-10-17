<?php

namespace Municipio\Customizer\Applicators\Cache;

use WpService\WpService;

/**
 * Generates cache keys based on customizer state and content signatures
 */
class CacheKeyGenerator implements CacheKeyGeneratorInterface
{
    private string $cacheKeyBaseName = 'theme_mod_applicator_cache';

    public function __construct(
        private WpService $wpService,
        private SignatureGeneratorInterface $signatureGenerator
    ) {
    }

    /**
     * Generate a cache key based on customizer state and content signature
     *
     * @return string The generated cache key
     */
    public function generateCacheKey(): string
    {
        return sprintf(
            '%s_%s_%s_%s%s',
            $this->cacheKeyBaseName,
            $this->getCustomizerStateKey(),
            $this->signatureGenerator->getLastPublishedTimestamp(),
            $this->signatureGenerator->getCustomizerFieldSignature(),
            $this->getCacheKeySuffix()
        );
    }

    /**
     * Get the customizer state key
     *
     * @return string
     */
    private function getCustomizerStateKey(): string
    {
        return $this->wpService->isCustomizePreview() ? 'draft' : 'publish';
    }

    /**
     * Get the cache key suffix
     *
     * @return string
     */
    private function getCacheKeySuffix(): string
    {
        return $this->wpService->applyFilters(
            'Municipio/Customizer/CacheKeySuffix',
            '',
            $this->wpService->isCustomizePreview(),
            $this->wpService->getPostType()
        );
    }
}