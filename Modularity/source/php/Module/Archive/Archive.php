<?php

namespace Modularity\Module\Archive;

use Modularity\Helper\WpService;
use Municipio\PostsList\Config\AppearanceConfig\DefaultAppearanceConfig;
use Municipio\PostsList\Config\DefaultPostsListConfig;
use Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\Config\PostsListConfigInterface;
use Municipio\PostsList\PostList;

class Archive extends \Modularity\Module
{
    public $slug              = 'archive';
    public $isBlockCompatible = true;
    private PostList $postList;

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
        $this->templateDir = $this->getPostList()->getTemplateDir();
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

    private function getPostList(): PostList
    {
        if (!isset($this->postList)) {
            $this->postList = new PostList($this->createPostListConfig(), new DefaultAppearanceConfig(), WpService::get());
        }

        return $this->postList;
    }
}
