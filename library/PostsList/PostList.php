<?php

namespace Municipio\PostsList;

use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\AppearanceConfig\PostDesign;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\GetPosts\GetPostsFromPostsListConfig;
use WpService\Contracts\GetPosts;

class PostList
{
    /**
     * @var PostObjectInterface[]
     */
    private array $posts;

    public function __construct(
        private GetPostsConfigInterface $getPostsConfig,
        private AppearanceConfigInterface $appearanceConfig,
        private GetPosts $wpService
    ) {
    }

    public function getTemplateDir(): string
    {
        return __DIR__ . '/views/';
    }

    /**
     * Get data for rendering
     *
     * @return array
     */
    public function getData(): array
    {
        return ['posts' => $this->getPosts(), 'config' => $this->getAppearanceConfig()];
    }

    /**
     * Get posts based on config
     *
     * @return PostObjectInterface[]
     */
    private function getPosts(): array
    {
        if (!isset($this->posts)) {
            $this->posts = (new GetPostsFromPostsListConfig($this->getPostsConfig, $this->wpService))->getPosts();
        }

        return $this->posts;
    }

    private function getAppearanceConfig(): AppearanceConfigInterface
    {
        $shouldDisplayPlaceholderImage = (new AnyPostHasImage())->check(...$this->getPosts());
        return new class ($shouldDisplayPlaceholderImage, $this->appearanceConfig) implements AppearanceConfigInterface {
            public function __construct(
                private bool $shouldDisplayPlaceholderImage,
                private AppearanceConfigInterface $innerConfig
            ) {
            }
            public function shouldDisplayPlaceholderImage(): bool
            {
                return $this->shouldDisplayPlaceholderImage;
            }
            public function getDesign(): PostDesign
            {
                return $this->innerConfig->getDesign();
            }
            public function shouldDisplayReadingTime(): bool
            {
                return $this->innerConfig->shouldDisplayReadingTime();
            }
        };
    }
}
