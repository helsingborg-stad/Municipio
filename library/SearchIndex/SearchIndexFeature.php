<?php

declare(strict_types=1);

namespace Municipio\SearchIndex;

use AcfService\Contracts\AddOptionsPage;
use AcfService\Contracts\GetField;
use AcfService\Contracts\UpdateField;
use Municipio\Helper\AdminNotices\AdminNoticesInterface;
use Municipio\SearchIndex\Config\SearchIndexConfig;
use Municipio\SearchIndex\Provider\SearchProviderFactory;
use WpService\WpService;
use WpUtilService\Features\Enqueue\EnqueueManagerInterface;

/**
 * Enables indexing and querying content through the configured search provider.
 */
class SearchIndexFeature
{
    private ?SearchIndexConfig $config = null;
    private ?SearchProviderFactory $providerFactory = null;

    public function __construct(
        private WpService $wpService,
        private GetField&UpdateField&AddOptionsPage $acfService,
        private EnqueueManagerInterface $enqueue,
        private AdminNoticesInterface $adminNoticesService,
    ) {}

    /**
     * Register the feature hooks when a search provider has been configured.
     */
    public function enable(): void
    {
        $legacyPluginConflictGuard = new Migration\LegacyPluginConflictGuard($this->wpService);

        if ($legacyPluginConflictGuard->deactivateConflictingPlugins()) {
            (new Migration\LegacySettingsMigration(
                $this->wpService,
                $this->acfService,
            ))->migrateAttachmentActivation();
            return;
        }

        if ($this->wpService->didAction('acf/init') > 0) {
            $this->initialize();
            return;
        }

        $this->wpService->addAction('acf/init', [$this, 'initialize'], 20);
    }

    /**
     * Initialize the feature after ACF is ready.
     */
    public function initialize(): void
    {
        $this->config = new SearchIndexConfig($this->acfService);
        $this->providerFactory = new SearchProviderFactory($this->wpService, $this->config);

        (new Provider\Algolia\AlgoliaProviderRegistrar($this->wpService, $this->config))->addHooks();
        (new Provider\Typesense\TypesenseProviderRegistrar($this->wpService, $this->config))->addHooks();
        (new Admin\SearchIndexSettings($this->wpService, $this->acfService, $this->config, $this->providerFactory, $this->adminNoticesService))->addHooks();
        (new Admin\ExcludeFromSearch($this->wpService))->addHooks();
        (new Facets\FacetsFeature($this->wpService, new Config\FacetsConfig($this->acfService)))->addHooks();

        if (defined('WP_CLI') && constant('WP_CLI') === true) {
            (new Cli\BuildSearchIndexCommand($this->wpService, $this->config, $this->providerFactory))->register();
            (new Cli\PrepareSearchIndexCommand($this->config, $this->providerFactory))->register();
            (new Cli\CheckSearchIndexCommand($this->config, $this->providerFactory))->register();
        }

        if ($this->wpService->didAction('init') > 0) {
            $this->initializeConfiguredFeatures();
            return;
        }

        $this->wpService->addAction('init', [$this, 'initializeConfiguredFeatures'], 20);
    }

    /**
     * Initialize features that require effective provider settings.
     */
    public function initializeConfiguredFeatures(): void
    {
        if ($this->config === null || $this->providerFactory === null || !$this->config->isConfigured()) {
            return;
        }

        $provider = $this->providerFactory->create();
        (new Index\PostIndexer($this->wpService, $provider))->addHooks();

        $attachmentConfig = new Attachment\AttachmentConfig($this->acfService);
        (new Attachment\AttachmentFeature($this->wpService, $attachmentConfig))->addHooks();

        (new Search\SearchQuery($this->wpService, $provider))->addHooks();

        if ((new SearchPage\SearchPageConfig($this->acfService))->isEnabled()) {
            (new SearchPage\SearchPageFeature($this->wpService, $this->enqueue, $this->config))->addHooks();
        }
    }
}