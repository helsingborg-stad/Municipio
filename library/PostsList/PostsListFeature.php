<?php

namespace Municipio\PostsList;

use WpService\Contracts\AddFilter;

class PostsListFeature
{
    public function __construct(private AddFilter $wpService)
    {
    }
    /**
     * Enable the Posts List feature.
     */
    public function enable(): void
    {
        // Register view path for posts list templates
        $this->wpService->addFilter('Municipio/viewPaths', fn($paths) => [...$paths, self::getTemplateDir()]);
    }

    public static function getTemplateDir(): string
    {
        return __DIR__ . '/views';
    }
}
