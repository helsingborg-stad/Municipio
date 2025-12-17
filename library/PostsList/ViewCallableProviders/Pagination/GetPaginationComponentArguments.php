<?php

namespace Municipio\PostsList\ViewCallableProviders\Pagination;

use Municipio\PostsList\ViewCallableProviders\ViewCallableProviderInterface;

/**
 * Provides arguments for pagination component
 */
class GetPaginationComponentArguments implements ViewCallableProviderInterface
{
    private const MINIMUM_PAGES_FOR_PAGINATION = 2;

    /**
     * @param int $totalPages
     * @param int $currentPage
     * @param string $paginationQueryParam
     * @param string $id
     */
    public function __construct(
        private int $totalPages,
        private int $currentPage,
        private string $paginationQueryParam,
        private string $id,
    ) {}

    /**
     * Returns a callable that provides pagination arguments.
     *
     * @return callable
     */
    public function getCallable(): callable
    {
        return function (): array {
            if ($this->totalPages < self::MINIMUM_PAGES_FOR_PAGINATION) {
                return [];
            }

            return [
                'list' => $this->generatePaginationList(),
                'current' => $this->currentPage,
                'linkPrefix' => $this->paginationQueryParam,
            ];
        };
    }

    /**
     * Generates the pagination list.
     *
     * @return array
     */
    private function generatePaginationList(): array
    {
        $paginationList = [];
        for ($page = 1; $page <= $this->totalPages; $page++) {
            $paginationList[] = [
                'href' => $this->buildPageUrl($page) . '#' . $this->id,
                'label' => (string) $page,
            ];
        }
        return $paginationList;
    }

    /**
     * Builds a URL with the updated page parameter.
     *
     * @param int $page
     * @return string
     */
    private function buildPageUrl(int $page): string
    {
        $url = $this->getCurrentRequestUri();
        $parsedUrl = parse_url($url);
        $queryParams = [];

        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $queryParams);
        }

        $queryParams[$this->paginationQueryParam] = $page;
        $newQueryString = http_build_query($queryParams);
        $path = $parsedUrl['path'] ?? '';

        return $path . '?' . $newQueryString;
    }

    /**
     * Gets the current request URI.
     *
     * @return string
     */
    private function getCurrentRequestUri(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '';
    }
}
