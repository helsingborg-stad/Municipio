<?php

namespace Municipio\PostsList\ViewCallableProviders\Filter;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use WpService\Contracts\__;

/**
 * Provides arguments for filter submit button
 */
class GetFilterSubmitButtonArguments
{
    /**
     * Constructor
     */
    public function __construct(
        private GetPostsConfigInterface $getPostsConfig,
        private __ $wpService
    ) {
    }

    /**
     * Get callable
     */
    public function getCallable(): callable
    {
        return fn() => $this->getSubmitButtonArguments();
    }

    /**
     * Get submit button arguments
     */
    private function getSubmitButtonArguments(): array
    {
        return [
            'text' => $this->getText(),
            'type' => 'submit',
            'icon' => $this->getIcon(),
        ];
    }

    /**
     * Get text for submit button
     */
    private function getText(): string
    {
        return $this->getPostsConfig->isFacettingTaxonomyQueryEnabled()
            ? $this->wpService->__('Filter', 'municipio')
            : $this->wpService->__('Search', 'municipio');
    }

    /**
     * Get icon for submit button
     */
    private function getIcon(): string
    {
        return $this->getPostsConfig->isFacettingTaxonomyQueryEnabled()
            ? 'filter_list'
            : 'search';
    }
}
