<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Config;

use AcfService\Contracts\GetField;

/**
 * Reads search index settings from constants or ACF options.
 */
class SearchIndexConfig
{
    public function __construct(private GetField $acfService) {}

    public function provider(): ?string
    {
        $provider = $this->getOption('search_index_provider');
        return is_string($provider) && $provider !== '' ? $provider : 'algolia';
    }

    public function algoliaApplicationId(): string
    {
        return $this->getStringSetting('SEARCH_INDEX_ALGOLIA_APPLICATION_ID', 'search_index_algolia_application_id');
    }

    public function algoliaApiKey(): string
    {
        return $this->getStringSetting('SEARCH_INDEX_ALGOLIA_API_KEY', 'search_index_algolia_api_key');
    }

    public function indexName(): string
    {
        $configuredName = $this->getStringSetting('SEARCH_INDEX_NAME', 'search_index_name');

        if ($configuredName !== '') {
            return $configuredName;
        }

        $host = parse_url(home_url(), PHP_URL_HOST);
        return is_string($host) ? str_replace('.', '-', $host) : '';
    }

    public function isConfigured(): bool
    {
        return $this->provider() !== 'algolia' || ($this->algoliaApplicationId() !== '' && $this->algoliaApiKey() !== '');
    }

    private function getStringSetting(string $constant, string $field): string
    {
        if (defined($constant) && is_string(constant($constant))) {
            return constant($constant);
        }

        $value = $this->getOption($field);
        return is_string($value) ? $value : '';
    }

    private function getOption(string $field): mixed
    {
        return $this->acfService->getField($field, 'option');
    }
}