<?php

namespace Municipio\Customizer\Applicators\Cache;

use wpdb;
use WpService\WpService;

/**
 * Factory for creating cache-related components with proper dependency injection
 * 
 * This follows the Dependency Inversion Principle by creating abstractions
 * and injecting dependencies rather than creating them directly.
 */
class CacheComponentFactory
{
    public function __construct(
        private WpService $wpService,
        private wpdb $wpdb
    ) {
    }

    /**
     * Create a signature generator
     *
     * @return SignatureGeneratorInterface
     */
    public function createSignatureGenerator(): SignatureGeneratorInterface
    {
        return new SignatureGenerator($this->wpService, $this->wpdb);
    }

    /**
     * Create a cache key generator
     *
     * @param SignatureGeneratorInterface|null $signatureGenerator
     * @return CacheKeyGeneratorInterface
     */
    public function createCacheKeyGenerator(?SignatureGeneratorInterface $signatureGenerator = null): CacheKeyGeneratorInterface
    {
        $signatureGenerator = $signatureGenerator ?? $this->createSignatureGenerator();
        return new CacheKeyGenerator($this->wpService, $signatureGenerator);
    }

    /**
     * Create a cache storage
     *
     * @return CacheStorageInterface
     */
    public function createCacheStorage(): CacheStorageInterface
    {
        return new CacheStorage($this->wpService, $this->wpdb);
    }

    /**
     * Create a cache manager
     *
     * @param CacheKeyGeneratorInterface|null $keyGenerator
     * @param CacheStorageInterface|null $storage
     * @param SignatureGeneratorInterface|null $signatureGenerator
     * @return CacheManagerInterface
     */
    public function createCacheManager(
        ?CacheKeyGeneratorInterface $keyGenerator = null,
        ?CacheStorageInterface $storage = null,
        ?SignatureGeneratorInterface $signatureGenerator = null
    ): CacheManagerInterface {
        $signatureGenerator = $signatureGenerator ?? $this->createSignatureGenerator();
        $keyGenerator = $keyGenerator ?? $this->createCacheKeyGenerator($signatureGenerator);
        $storage = $storage ?? $this->createCacheStorage();

        return new CacheManager($keyGenerator, $storage, $signatureGenerator);
    }
}