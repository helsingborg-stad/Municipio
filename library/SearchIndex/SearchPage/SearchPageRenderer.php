<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\SearchPage;

use ComponentLibrary\Renderer\RendererInterface;
use WpService\WpService;

/**
 * Renders the SearchIndex interactive search page and client templates.
 */
class SearchPageRenderer
{
    private bool $hasRendered = false;

    public function __construct(
        private RendererInterface $renderer,
        private WpService $wpService,
    ) {}

    /**
     * Render the search page once per request.
     */
    public function render(): string
    {
        if ($this->hasRendered) {
            return '';
        }

        $this->hasRendered = true;
        return $this->renderer->render('search-page', [
            'lang' => [
                'searchLabel' => $this->wpService->__('What are you looking for?', 'municipio'),
                'noresults' => $this->wpService->__('No search results found.', 'municipio'),
                'stats' => $this->wpService->__('%s search results found.', 'municipio'),
                'nofacets' => $this->wpService->__('No filters are available for this search.', 'municipio'),
                'openFilters' => $this->wpService->__('Filter', 'municipio'),
                'pagination' => $this->wpService->__('Pagination', 'municipio'),
            ],
        ]);
    }

    /**
     * Get the feature-owned Blade view directory.
     */
    public static function getViewPath(): string
    {
        return __DIR__ . '/views/';
    }
}