<?php

namespace Municipio\PostsList\ViewCallableProviders\Filter;

use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use WpService\Contracts\__;

class GetFilterResetButtonArguments
{
    public function __construct(
        private GetPostsConfigInterface $getPostsConfig,
        private FilterConfigInterface $filterConfig,
        private __ $wpService
    ) {
    }

    public function getCallable(): callable
    {
        return fn() => $this->getSubmitButtonArguments();
    }

    private function getSubmitButtonArguments(): array
    {
        return [
            'text' => $this->getText(),
            'type' => 'basic',
            'href' => $this->filterConfig->getResetUrl(),
        ];
    }

    private function getText(): string
    {
        return $this->getPostsConfig->isFacettingTaxonomyQueryEnabled()
            ? $this->wpService->__('Reset filter', 'municipio')
            : $this->wpService->__('Reset search', 'municipio');
    }
}
