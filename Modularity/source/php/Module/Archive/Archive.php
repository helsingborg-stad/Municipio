<?php

namespace Modularity\Module\Archive;

use Modularity\Helper\WpService;
use Municipio\PostsList\Config\AppearanceConfig\DefaultAppearanceConfig;
use Municipio\PostsList\Config\FilterConfig\DefaultFilterConfig;
use Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\PostsList;
use Municipio\PostsList\PostsListFeature;

/*
 * Archive module class
 */
class Archive extends \Modularity\Module
{
    public $slug              = 'archive';
    public $isBlockCompatible = true;
    private PostsList $postsList;

    /**
     * Initialize the module
     */
    public function init(): void
    {
        $wpService          = WpService::get();
        $this->nameSingular = $wpService->__("Archive", 'municipio');
        $this->namePlural   = $wpService->__("Archives", 'municipio');
        $this->description  = $wpService->__("Outputs an archive module.", 'municipio');
        $this->setTemplateDirectory();
    }

    /**
     * Set the template directory for the module
     */
    private function setTemplateDirectory(): void
    {
        $this->templateDir = PostsListFeature::getTemplateDir();
        WpService::get()->addFilter(
            '/Modularity/externalViewPath',
            fn($paths) => [...$paths, 'mod-' . $this->slug => $this->templateDir]
        );
    }

    /**
     * Get the data for the module
     *
     * @return array
     */
    public function data(): array
    {
        return $this->getPostList()->getData();
    }

    /**
     * Create the configuration for fetching posts
     *
     * @return GetPostsConfigInterface
     */
    private function createPostListConfig(): GetPostsConfigInterface
    {
        return new DefaultGetPostsConfig();
    }

    /**
     * Get the template file for the module
     *
     * @return string
     */
    public function template(): string
    {
        return 'posts-list.blade.php';
    }

    /**
     * Get the PostsList instance for the module
     *
     * @return PostsList
     */
    private function getPostList(): PostsList
    {
        if (!isset($this->postsList)) {
            $this->postsList = new PostsList($this->createPostListConfig(), new DefaultAppearanceConfig(), new DefaultFilterConfig(), WpService::get());
        }

        return $this->postsList;
    }
}
