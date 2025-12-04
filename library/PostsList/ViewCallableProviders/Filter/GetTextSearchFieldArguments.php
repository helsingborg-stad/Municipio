<?php

namespace Municipio\PostsList\ViewCallableProviders\Filter;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\ViewCallableProviders\ViewCallableProviderInterface;
use WpService\Contracts\__;
use WpService\Contracts\GetPostTypeObject;

/**
 * Provides arguments for text search field
 */
class GetTextSearchFieldArguments implements ViewCallableProviderInterface
{
    /**
     * Constructor
     */
    public function __construct(
        private GetPostsConfigInterface $getPostsConfig,
        private string $parameterName,
        private __&GetPostTypeObject $wpService
    ) {
    }

    /**
     * Get callable
     */
    public function getCallable(): callable
    {
        return fn() => $this->buildTextSearchFieldArguments();
    }

    /**
     * Build arguments for text search field
     */
    private function buildTextSearchFieldArguments(): array
    {
        $arguments = [
            'type'     => 'search',
            'name'     => $this->parameterName,
            'label'    => $this->resolveLabel(),
            'required' => false,
        ];

        return array_merge($arguments, $this->getSearchValue());
    }

    /**
     * Get search value if available
     */
    private function getSearchValue(): array
    {
        return !empty($this->getPostsConfig->getSearch())
            ? ['value' => $this->getPostsConfig->getSearch()]
            : [];
    }

    /**
     * Resolve label for search field
     */
    private function resolveLabel(): string
    {
        return $this->getLabelFromSinglePostType() !== null
            ? $this->getLabelFromSinglePostType()
            : $this->getDefaultLabel();
    }

    /**
     * Get default label
     */
    private function getDefaultLabel(): string
    {
        return $this->wpService->__('Search', 'municipio');
    }

    /**
     * Get label from single post type if applicable
     */
    private function getLabelFromSinglePostType(): ?string
    {
        $postTypes = $this->getPostsConfig->getPostTypes();

        if (count($postTypes) !== 1) {
            return null;
        }

        $postTypeObject = $this->wpService->getPostTypeObject(reset($postTypes));

        if (!$postTypeObject || empty($postTypeObject->label)) {
            return null;
        }

        return sprintf(
            $this->wpService->__('Search %s', 'municipio'),
            $postTypeObject->label
        );
    }
}
