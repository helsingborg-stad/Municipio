<?php

namespace Municipio\PostsList\ViewCallableProviders\Filter;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use WpService\Contracts\__;

class GetFilterSubmitButtonArguments
{
    public function __construct(
        private GetPostsConfigInterface $getPostsConfig,
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
            'type' => 'submit',
            'icon' => $this->getIcon(),
        ];
    }

    private function getText(): string
    {
        return $this->getPostsConfig->isFacettingTaxonomyQueryEnabled()
            ? $this->wpService->__('Filter', 'municipio')
            : $this->wpService->__('Search', 'municipio');
    }

    private function getIcon(): string
    {
        return $this->getPostsConfig->isFacettingTaxonomyQueryEnabled()
            ? 'filter_list'
            : 'search';
    }
}
