<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Config;

use AcfService\Contracts\GetField;
use Municipio\Helper\Constant\Constant;
use Municipio\Helper\Constant\ConstantInterface;

/**
 * Reads search index settings from constants or ACF options.
 */
class SearchIndexConfig
{
    public function __construct(private GetField $acfService, private ConstantInterface $constantService = new Constant()) {}

    public function provider(): ?string
    {
        return $this->getStringSetting('search_index_provider', 'SEARCH_INDEX_PROVIDER') ?: 'algolia';
    }

    public function algoliaApplicationId(): string
    {
        return $this->getStringSetting('search_index_algolia_application_id', 'SEARCH_INDEX_ALGOLIA_APPLICATION_ID', 'ALGOLIAINDEX_APPLICATION_ID');
    }

    public function algoliaApiKey(): string
    {
        return $this->getStringSetting('search_index_algolia_api_key', 'SEARCH_INDEX_ALGOLIA_API_KEY', 'ALGOLIAINDEX_API_KEY');
    }

    public function algoliaPublicApiKey(): string
    {
        return $this->getStringSetting('search_index_algolia_public_api_key', 'SEARCH_INDEX_ALGOLIA_PUBLIC_API_KEY', 'ALGOLIAINDEX_PUBLIC_API_KEY');
    }

    public function algoliaIndexName(): string
    {
        return $this->getConfiguredOrGeneratedName('search_index_algolia_index_name', 'SEARCH_INDEX_ALGOLIA_INDEX_NAME', 'ALGOLIAINDEX_INDEX_NAME');
    }

    public function typesenseApiUrl(): string
    {
        return rtrim($this->getStringSetting('search_index_typesense_api_url', 'SEARCH_INDEX_TYPESENSE_API_URL', 'TYPESENSEINDEX_API_URL'), '/');
    }

    public function typesenseApiKey(): string
    {
        return $this->getStringSetting('search_index_typesense_api_key', 'SEARCH_INDEX_TYPESENSE_API_KEY', 'TYPESENSEINDEX_API_KEY');
    }

    public function typesensePublicApiKey(): string
    {
        return $this->getStringSetting('search_index_typesense_public_api_key', 'SEARCH_INDEX_TYPESENSE_PUBLIC_API_KEY', 'TYPESENSEINDEX_PUBLIC_API_KEY');
    }

    public function typesenseCollectionName(): string
    {
        return $this->getConfiguredOrGeneratedName('search_index_typesense_collection_name', 'SEARCH_INDEX_TYPESENSE_COLLECTION_NAME', 'TYPESENSEINDEX_COLLECTION_NAME');
    }

    public function isConfigured(): bool
    {
        return $this->isProviderConfigured($this->provider());
    }

    public function isProviderConfigured(string $provider): bool
    {
        return match ($provider) {
            'algolia' => $this->algoliaApplicationId() !== '' && $this->algoliaApiKey() !== '',
            'typesense' => $this->typesenseApiUrl() !== '' && $this->typesenseApiKey() !== '',
            default => false,
        };
    }

    private function getStringSetting(string $field, string ...$constants): string
    {
        foreach ($constants as $constant) {
            $value = $this->constantService->defined($constant) ? $this->constantService->constant($constant) : null;

            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        $value = $this->getOption($field);
        return is_string($value) ? $value : '';
    }

    /**
     * Return a configured provider name or derive one from the site hostname.
     */
    private function getConfiguredOrGeneratedName(string $field, string ...$constants): string
    {
        $configuredName = $this->getStringSetting($field, ...$constants);

        if ($configuredName !== '') {
            return $configuredName;
        }

        $host = parse_url(home_url(), PHP_URL_HOST);
        return is_string($host) ? str_replace('.', '-', $host) : '';
    }

    private function getOption(string $field): mixed
    {
        return $this->acfService->getField($field, 'option');
    }
}