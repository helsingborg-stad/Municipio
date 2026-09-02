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
        $config = new SearchIndexConfig($this->acfService);
        $providerFactory = new SearchProviderFactory($this->wpService, $config);

        (new Provider\Algolia\AlgoliaProviderRegistrar($this->wpService, $config))->addHooks();
        (new Provider\Typesense\TypesenseProviderRegistrar($this->wpService, $config))->addHooks();
        (new Admin\SearchIndexSettings($this->wpService, $this->acfService, $config, $providerFactory, $this->adminNoticesService))->addHooks();
        (new Admin\ExcludeFromSearch($this->wpService))->addHooks();
        (new Facets\FacetsFeature($this->wpService, new Config\FacetsConfig($this->acfService)))->addHooks();

        if (defined('WP_CLI') && constant('WP_CLI') === true) {
            (new Cli\BuildSearchIndexCommand($this->wpService, $config, $providerFactory))->register();
        }

        if (!$config->isConfigured()) {
            return;
        }

        $provider = $providerFactory->create();
        (new Index\PostIndexer($this->wpService, $provider))->addHooks();

        $attachmentConfig = new Attachment\AttachmentConfig($this->acfService);
        (new Attachment\AttachmentFeature($this->wpService, $attachmentConfig))->addHooks();

        (new Search\SearchQuery($this->wpService, $provider))->addHooks();

        if ((new SearchPage\SearchPageConfig($this->acfService))->isEnabled()) {
            (new SearchPage\SearchPageFeature($this->wpService, $this->enqueue, $config))->addHooks();
        }
    }
}