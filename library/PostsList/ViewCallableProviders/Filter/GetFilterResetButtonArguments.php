<?php

namespace Municipio\PostsList\ViewCallableProviders\Filter;

use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use WpService\Contracts\__;

/**
 * Provides arguments for filter reset button
 */
class GetFilterResetButtonArguments
{
    /**
     * Constructor
     */
    public function __construct(
        private GetPostsConfigInterface $getPostsConfig,
        private FilterConfigInterface $filterConfig,
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
            'type' => 'basic',
            'href' => $this->filterConfig->getResetUrl(),
        ];
    }

    /**
     * Get text for submit button
     */
    private function getText(): string
    {
        return $this->getPostsConfig->isFacettingTaxonomyQueryEnabled()
            ? $this->wpService->__('Reset filter', 'municipio')
            : $this->wpService->__('Reset search', 'municipio');
    }
}
