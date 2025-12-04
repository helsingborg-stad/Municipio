<?php

namespace Municipio\PostsList;

use WpService\Contracts\AddFilter;

/*
 * Posts List feature class
 */
class PostsListFeature
{
    /**
     * Constructor
     *
     * @param AddFilter $wpService
     */
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

    /**
     * Get the template directory path
     *
     * @return string
     */
    public static function getTemplateDir(): string
    {
        return __DIR__ . '/views';
    }
}
