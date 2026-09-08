<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Admin\Indexing;

use Municipio\SearchIndex\Config\SearchIndexConfig;
use WpService\WpService;
use WpUtilService\Features\Enqueue\EnqueueManagerInterface;

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
        private EnqueueManagerInterface $enqueue,
        private SearchIndexConfig $config,
    ) {}

    /**
     * Register the indexing metabox and its page-specific assets.
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('acf/input/admin_head', [$this, 'registerMetaBox']);
        $this->wpService->addAction('admin_enqueue_scripts', [$this, 'enqueueAssets']);
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
        $disabled = $this->config->isConfigured() ? '' : ' disabled';
        $endpoint = $this->wpService->escUrl($this->wpService->adminUrl('admin-ajax.php'));
        $nonce = $this->wpService->escAttr($this->wpService->wpCreateNonce(SearchIndexingRequest::NONCE_ACTION));
        $label = $this->wpService->escHtml($this->wpService->__('Start indexing', 'municipio'));
        $errorMessage = $this->wpService->escAttr($this->wpService->__('An error occurred', 'municipio'));

        echo '<button type="button" class="button button-primary" data-search-index-build data-endpoint="'
            . $endpoint . '" data-nonce="' . $nonce . '" data-error-message="' . $errorMessage . '"'
            . $disabled . '>' . $label . '</button>';

        if ($disabled !== '') {
            echo '<p>' . $this->wpService->escHtml(
                $this->wpService->__('Configure and save a search provider before indexing.', 'municipio')
            ) . '</p>';
        }
    }

    /**
     * Enqueue indexing assets only on the Search Index settings page.
     */
    public function enqueueAssets(): void
    {
        if (!$this->isIndexSettingsPage() || !$this->wpService->currentUserCan('manage_options')) {
            return;
        }

        $this->enqueue->add('js/search-index-admin-indexing.js');
    }

    /**
     * Determine whether the current request renders the Search Index settings page.
     */
    private function isIndexSettingsPage(): bool
    {
        return ($_GET['page'] ?? '') === self::OPTIONS_PAGE_SLUG;
    }
}