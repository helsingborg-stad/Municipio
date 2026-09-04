<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\SearchPage;

use ComponentLibrary\Renderer\BladeService\BladeServiceFactory;
use ComponentLibrary\Renderer\Renderer as BladeRenderer;
use Municipio\SearchIndex\Config\SearchIndexConfig;
use WpService\WpService;
use WpUtilService\Features\Enqueue\EnqueueManagerInterface;

/**
 * Replaces the standard search results with a provider-backed interactive page.
 */
class SearchPageFeature
{
    private SearchPageRenderer $renderer;

    public function __construct(
        private WpService $wpService,
        private EnqueueManagerInterface $enqueue,
        private SearchIndexConfig $config,
    ) {
        $bladeRenderer = new BladeRenderer((new BladeServiceFactory($this->wpService))->create([
            SearchPageRenderer::getViewPath(),
        ]));
        $this->renderer = new SearchPageRenderer($bladeRenderer, $this->wpService);
    }

    /**
     * Register search-page rendering and asset hooks.
     */
    public function addHooks(): void
    {
        $defaultMountPoint = defined('ALGOLIA_INDEX_MOUNT_POINT') && is_string(constant('ALGOLIA_INDEX_MOUNT_POINT'))
            ? constant('ALGOLIA_INDEX_MOUNT_POINT')
            : 'custom_search_page';
        $mountPoint = $this->wpService->applyFilters('Municipio/SearchIndex/SearchPage/MountPoint', $defaultMountPoint);

        $this->wpService->addAction('wp_enqueue_scripts', [$this, 'enqueueAssets']);
        $this->wpService->addAction(is_string($mountPoint) ? $mountPoint : 'custom_search_page', [$this, 'renderSearchPage']);
        $this->wpService->addFilter('Municipio/SearchIndex/BackendSearchActive', [$this, 'disableBackendSearch']);
    }

    /**
     * Disable the redundant server-side provider query on the interactive search page.
     */
    public function disableBackendSearch(bool $active): bool
    {
        return $this->isActiveSearchPage() ? false : $active;
    }

    /**
     * Render the interactive UI at Municipio's custom search-page mount point.
     */
    public function renderSearchPage(): void
    {
        if (!$this->isActiveSearchPage()) {
            return;
        }

        echo $this->renderer->render();
    }

    /**
     * Enqueue subfeature assets and browser configuration on search pages.
     */
    public function enqueueAssets(): void
    {
        if (!$this->isActiveSearchPage()) {
            return;
        }

        $facets = $this->wpService->applyFilters('Municipio/SearchIndex/Facets', []);
        $browserConfig = $this->wpService->applyFilters('Municipio/SearchIndex/BrowserConfig', [
            'searchAsYouType' => true,
            'facetingEnabled' => (bool) $this->wpService->applyFilters('Municipio/SearchIndex/FacetingEnabled', false),
            'facets' => $facets,
        ]);
        $searchParams = $this->wpService->applyFilters('Municipio/SearchIndex/SearchPage/Params', [
            'query' => $this->wpService->getQueryVar('s'),
            'query_by' => 'post_title,post_excerpt,content',
            'page' => max(1, (int) $this->wpService->getQueryVar('paged')),
            'page_size' => 20,
            'highlight_full_fields' => 'post_title,post_excerpt',
        ]);

        $this->enqueue
            ->add('js/search-index-search-page.js')
            ->with()
            ->data('searchIndexSearchPageConfig', [
                'provider' => $browserConfig,
                'params' => $searchParams,
            ]);
        $this->enqueue->add('css/search-index-search-page.css');

        if (($browserConfig['type'] ?? null) === 'algolia') {
            $this->wpService->addFilter('WpSecurity/Csp', [$this, 'addAlgoliaCspDomains']);
        }
    }

    /**
     * Add Algolia browser API domains to CSP connect-src.
     */
    public function addAlgoliaCspDomains(array $directives): array
    {
        $directives['connect-src'] ??= [];
        $directives['connect-src'][] = 'https://*.algolianet.com';
        $directives['connect-src'][] = 'https://*.algolia.net';
        return $directives;
    }

    /**
     * Determine whether the interactive provider search page can run.
     */
    private function isActiveSearchPage(): bool
    {
        return !$this->wpService->isAdmin()
            && $this->wpService->isSearch()
            && $this->config->isConfigured()
            && $this->publicKeyIsConfigured();
    }

    private function publicKeyIsConfigured(): bool
    {
        return $this->config->provider() === 'typesense'
            ? $this->config->typesensePublicApiKey() !== ''
            : $this->config->algoliaPublicApiKey() !== '';
    }
}