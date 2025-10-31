<?php

namespace Modularity\Module\Archive;

use Modularity\Helper\WpService;
use Municipio\PostsList\Config\AppearanceConfig\DefaultAppearanceConfig;
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

    public function init(): void
    {
        $wpService          = WpService::get();
        $this->nameSingular = $wpService->__("Archive", 'municipio');
        $this->namePlural   = $wpService->__("Archives", 'municipio');
        $this->description  = $wpService->__("Outputs an archive module.", 'municipio');
        $this->setTemplateDirectory();
    }

    private function setTemplateDirectory(): void
    {
        $this->templateDir = PostsListFeature::getTemplateDir();
        WpService::get()->addFilter(
            '/Modularity/externalViewPath',
            fn($paths) => [...$paths, 'mod-' . $this->slug => $this->templateDir]
        );
    }

    public function data(): array
    {
        return $this->getPostList()->getData();
    }

    private function createPostListConfig(): GetPostsConfigInterface
    {
        return new DefaultGetPostsConfig();
    }

    public function template(): string
    {
        return 'posts-list.blade.php';
    }

    private function getPostList(): PostsList
    {
        if (!isset($this->postsList)) {
            $this->postsList = new PostsList($this->createPostListConfig(), new DefaultAppearanceConfig(), WpService::get());
        }

        return $this->postsList;
    }
}
