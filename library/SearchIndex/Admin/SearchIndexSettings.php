<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Admin;

use AcfService\Contracts\AddOptionsPage;
use AcfService\Contracts\GetField;
use Municipio\SearchIndex\Config\SearchIndexConfig;
use Municipio\SearchIndex\Provider\SearchProviderFactory;
use WpService\WpService;

/**
 * Registers and maintains the Search Index settings page.
 */
class SearchIndexSettings
{
    private const OPTIONS_PAGE_SLUG = 'municipio-search-index-settings';

    public function __construct(
        private WpService $wpService,
        private GetField&AddOptionsPage $acfService,
        private SearchIndexConfig $config,
        private SearchProviderFactory $providerFactory,
    ) {}

    /**
     * Register settings page, fields, and settings synchronization hooks.
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'registerOptionsPage']);
        $this->wpService->addAction('acf/save_post', [$this, 'sendProviderSettings'], 20);
        $this->wpService->addFilter('acf/load_field/name=search_index_provider', [$this, 'addProviderChoices']);
        $this->wpService->addFilter('Municipio/AcfExportManager/autoExport', [$this, 'registerAcfExports']);
    }

    /**
     * Register the ACF options page.
     */
    public function registerOptionsPage(): void
    {
        $this->acfService->addOptionsPage([
            'page_title' => $this->wpService->__('Search Index', 'municipio'),
            'menu_title' => $this->wpService->__('Search Index', 'municipio'),
            'menu_slug' => self::OPTIONS_PAGE_SLUG,
            'capability' => 'manage_options',
            'parent_slug' => 'options-general.php',
        ]);
    }

    /**
     * Add factories registered by search providers to the select field.
     */
    public function addProviderChoices(array $field): array
    {
        foreach (array_keys($this->providerFactory->getProviders()) as $provider) {
            $field['choices'][$provider] = ucfirst($provider);
        }

        return $field;
    }

    /**
     * Register ACF exports for automatic import by Municipio.
     */
    public function registerAcfExports(array $autoExportIds): array
    {
        $autoExportIds['search-index-settings'] = 'group_municipio_search_index_settings';
        $autoExportIds['search-index-facet-settings'] = 'group_municipio_search_index_facet_settings';

        return $autoExportIds;
    }

    /**
     * Send provider settings after saving the Search Index options page.
     */
    public function sendProviderSettings(string|int $postId): void
    {
        if ($postId !== 'options' || ($_GET['page'] ?? '') !== self::OPTIONS_PAGE_SLUG || !$this->config->isConfigured()) {
            return;
        }

        $this->providerFactory->create()->setSettings();
    }
}