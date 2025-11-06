<?php

namespace Municipio\PostsList\ViewCallableProviders\Filter;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\ViewCallableProviders\ViewCallableProviderInterface;
use WpService\Contracts\__;
use WpService\Contracts\GetPostTypeObject;

class GetTextSearchFieldArguments implements ViewCallableProviderInterface
{
    public function __construct(
        private GetPostsConfigInterface $getPostsConfig,
        private __&GetPostTypeObject $wpService
    ) {
    }

    public function getCallable(): callable
    {
        return fn() => $this->buildTextSearchFieldArguments();
    }

    private function buildTextSearchFieldArguments(): array
    {
        $arguments = [
            'type'     => 'search',
            'name'     => 's',
            'label'    => $this->resolveLabel(),
            'required' => false,
        ];

        return array_merge($arguments, $this->getSearchValue());
    }

    private function getSearchValue(): array
    {
        return !empty($this->getPostsConfig->getSearch())
            ? ['value' => $this->getPostsConfig->getSearch()]
            : [];
    }

    private function resolveLabel(): string
    {
        return $this->getLabelFromSinglePostType() !== null
            ? $this->getLabelFromSinglePostType()
            : $this->getDefaultLabel();
    }

    private function getDefaultLabel(): string
    {
        return $this->wpService->__('Search', 'municipio');
    }

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
