<?php

namespace Municipio\PostsList\ViewCallableProviders\Pagination;

use Municipio\PostsList\ViewCallableProviders\ViewCallableProviderInterface;

class GetPaginationComponentArguments implements ViewCallableProviderInterface
{
    public function __construct(
        private int $totalPages,
        private int $currentPage,
        private string $paginationQueryParam
    ) {
    }

    public function getCallable(): callable
    {
        return function () {
            $paginationList = [];
            for ($i = 1; $i <= $this->totalPages; $i++) {
                $paginationList[] = [
                    'href'  => $this->getUrlWithPage($i),
                    'label' => (string) $i,
                ];
            }

            return [
                'list'       => $paginationList,
                'current'    => $this->currentPage,
                'linkPrefix' => $this->paginationQueryParam
            ];
        };
    }

    private function getUrlWithPage(int $page): string
    {
        $url         = $_SERVER['REQUEST_URI'];
        $parsedUrl   = parse_url($url);
        $queryParams = [];
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $queryParams);
        }
        $queryParams[$this->paginationQueryParam] = $page;
        $newQueryString                           = http_build_query($queryParams);
        $finalUrl                                 = $parsedUrl['path'] . '?' . $newQueryString;
        return $finalUrl;
    }
}
