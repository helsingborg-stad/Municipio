<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Config;

use AcfService\Contracts\GetField;
use Municipio\Helper\Constant\Constant;
use Municipio\Helper\Constant\ConstantInterface;

/**
 * Reads search index settings from constants or ACF options.
 *
 * @mago-expect lint:too-many-methods
 */
class SearchIndexConfig
{
    private const CONFIGURATION_CONSTANTS = [
        'search_index_provider' => ['SEARCH_INDEX_PROVIDER'],
        'search_index_algolia_application_id' => ['SEARCH_INDEX_ALGOLIA_APPLICATION_ID', 'ALGOLIAINDEX_APPLICATION_ID'],
        'search_index_algolia_api_key' => ['SEARCH_INDEX_ALGOLIA_API_KEY', 'ALGOLIAINDEX_API_KEY'],
        'search_index_algolia_public_api_key' => ['SEARCH_INDEX_ALGOLIA_PUBLIC_API_KEY', 'ALGOLIAINDEX_PUBLIC_API_KEY'],
        'search_index_algolia_index_name' => ['SEARCH_INDEX_ALGOLIA_INDEX_NAME', 'ALGOLIAINDEX_INDEX_NAME'],
        'search_index_typesense_api_url' => ['SEARCH_INDEX_TYPESENSE_API_URL', 'TYPESENSEINDEX_API_URL'],
        'search_index_typesense_api_key' => ['SEARCH_INDEX_TYPESENSE_API_KEY', 'TYPESENSEINDEX_API_KEY'],
        'search_index_typesense_public_api_key' => ['SEARCH_INDEX_TYPESENSE_PUBLIC_API_KEY', 'TYPESENSEINDEX_PUBLIC_API_KEY'],
        'search_index_typesense_collection_name' => ['SEARCH_INDEX_TYPESENSE_COLLECTION_NAME', 'TYPESENSEINDEX_COLLECTION_NAME'],
    ];

    public function __construct(private GetField $acfService, private ConstantInterface $constantService = new Constant()) {}

    public function provider(): ?string
    {
        return $this->getStringSetting('search_index_provider') ?: 'algolia';
    }

    public function algoliaApplicationId(): string
    {
        return $this->getStringSetting('search_index_algolia_application_id');
    }

    public function algoliaApiKey(): string
    {
        return $this->getStringSetting('search_index_algolia_api_key');
    }

    public function algoliaPublicApiKey(): string
    {
        return $this->getStringSetting('search_index_algolia_public_api_key');
    }

    public function algoliaIndexName(): string
    {
        return $this->getConfiguredOrGeneratedName('search_index_algolia_index_name');
    }

    public function typesenseApiUrl(): string
    {
        return rtrim($this->getStringSetting('search_index_typesense_api_url'), '/');
    }

    public function typesenseApiKey(): string
    {
        return $this->getStringSetting('search_index_typesense_api_key');
    }

    public function typesensePublicApiKey(): string
    {
        return $this->getStringSetting('search_index_typesense_public_api_key');
    }

    public function typesenseCollectionName(): string
    {
        return $this->getConfiguredOrGeneratedName('search_index_typesense_collection_name');
    }

    /**
     * Return the constant that currently overrides an ACF setting.
     */
    public function overridingConstant(string $field): ?string
    {
        foreach (self::CONFIGURATION_CONSTANTS[$field] ?? [] as $constant) {
            $value = $this->constantService->defined($constant) ? $this->constantService->constant($constant) : null;

            if (is_string($value) && $value !== '') {
                return $constant;
            }
        }

        return null;
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

    private function getStringSetting(string $field): string
    {
        $overridingConstant = $this->overridingConstant($field);

        if ($overridingConstant !== null) {
            $value = $this->constantService->constant($overridingConstant);
            return is_string($value) ? $value : '';
        }

        $value = $this->getOption($field);
        return is_string($value) ? $value : '';
    }

    /**
     * Return a configured provider name or derive one from the site hostname.
     */
    private function getConfiguredOrGeneratedName(string $field): string
    {
        $configuredName = $this->getStringSetting($field);

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