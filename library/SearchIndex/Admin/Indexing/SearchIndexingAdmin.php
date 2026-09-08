<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Admin\Indexing;

use Municipio\ProgressReporter\UI\AdminProgressActionButton;
use Municipio\ProgressReporter\UI\AdminProgressActionButtonConfig;
use Municipio\ProgressReporter\UI\AdminProgressActionButtonState;
use Municipio\SearchIndex\Config\SearchIndexConfig;
use WpService\WpService;

/**
 * Adds Search Index indexing controls to the settings page.
 */
class SearchIndexingAdmin
{
    private const OPTIONS_PAGE_SLUG = 'municipio-search-index-settings';

    /**
     * Create the Search Index admin UI integration.
     */
    public function __construct(
        private WpService $wpService,
        private SearchIndexConfig $config,
        private AdminProgressActionButton $progressActionButton,
    ) {}

    /**
     * Register the indexing metabox and its page-specific assets.
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('acf/input/admin_head', [$this, 'registerMetaBox']);
    }

    /**
     * Register the indexing action metabox for authorized administrators.
     */
    public function registerMetaBox(): void
    {
        if (!$this->isIndexSettingsPage() || !$this->wpService->currentUserCan('manage_options')) {
            return;
        }

        $this->wpService->addMetaBox(
            'municipio-search-index-indexing',
            $this->wpService->__('Update Search Index', 'municipio'),
            [$this, 'render'],
            'acf_options_page',
            'side',
            'high',
        );
    }

    /**
     * Render the indexing action.
     */
    public function render(): void
    {
        $state = $this->config->isConfigured()
            ? AdminProgressActionButtonState::Enabled
            : AdminProgressActionButtonState::Disabled;

        echo $this->progressActionButton->renderPost(new AdminProgressActionButtonConfig(
            action: SearchIndexingRequest::ACTION,
            label: $this->wpService->__('Start indexing', 'municipio'),
            state: $state,
            errorMessage: $this->wpService->__('An error occurred', 'municipio'),
        ));

        if ($state === AdminProgressActionButtonState::Disabled) {
            echo '<p>' . $this->wpService->escHtml(
                $this->wpService->__('Configure and save a search provider before indexing.', 'municipio')
            ) . '</p>';
        }
    }

    /**
     * Determine whether the current request renders the Search Index settings page.
     */
    private function isIndexSettingsPage(): bool
    {
        return ($_GET['page'] ?? '') === self::OPTIONS_PAGE_SLUG;
    }
}