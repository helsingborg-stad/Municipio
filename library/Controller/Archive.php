<?php

namespace Municipio\Controller;

use Municipio\Controller\Navigation\Config\MenuConfig;
use Municipio\SchemaData\Utils\SchemaToPostTypesResolver\SchemaToPostTypeResolver;
use Municipio\Controller\Archive\AsyncConfigBuilderFactory;
use Municipio\Controller\Archive\AsyncConfigBuilder;

/**
 * Class Archive
 *
 * Follows Dependency Inversion Principle - factory can be injected or uses default implementation.
 *
 * @package Municipio\Controller
 */
class Archive extends \Municipio\Controller\BaseController
{
    private ?AsyncConfigBuilderFactory $asyncConfigFactory = null;

    /**
     * Get or create the async config factory.
     *
     * Allows for dependency injection while maintaining backward compatibility.
     *
     * @return AsyncConfigBuilderFactory
     */
    protected function getAsyncConfigFactory(): AsyncConfigBuilderFactory
    {
        if ($this->asyncConfigFactory === null) {
            $this->asyncConfigFactory = new AsyncConfigBuilderFactory(new AsyncConfigBuilder());
        }

        return $this->asyncConfigFactory;
    }

    /**
     * Set the async config factory (for dependency injection).
     *
     * @param AsyncConfigBuilderFactory $factory
     * @return void
     */
    public function setAsyncConfigFactory(AsyncConfigBuilderFactory $factory): void
    {
        $this->asyncConfigFactory = $factory;
    }
    /**
     * Initializes the Archive controller.
     *
     * This method is responsible for initializing the Archive controller and setting up the necessary data for the archive page.
     * It retrieves the current post type, gets the archive properties, sets the template, retrieves the posts, sets the query parameters,
     * retrieves the taxonomy filters, enables text search and date filter, determines the faceting type, sets the display options for featured image and reading time,
     * retrieves the current term meta, retrieves the archive data, sets the pagination, determines whether to show pagination, display functions, and filter reset,
     * determines whether to show the date pickers, determines whether to show the filter, and retrieves the archive menu items.
     */
    public function init()
    {
        parent::init();

        // Get current post type
        $postType = !empty($this->data['postType']) ? $this->data['postType'] : 'page';

        $this->data['displayArchiveLoop'] = true;

        // Get archive properties
        $this->data['archiveProps'] = $this->getArchiveProperties($postType, $this->data['customizer']);

        //Archive data
        $this->data['archiveTitle'] = $this->getArchiveTitle($this->data['archiveProps']);
        $this->data['archiveLead'] = $this->getArchiveLead($this->data['archiveProps']);

        // Build archive menu
        $archiveMenuConfig = new MenuConfig('archive-menu', $postType . '-menu');
        $this->menuBuilder->setConfig($archiveMenuConfig);
        $this->menuDirector->buildStandardMenu();
        $this->data['archiveMenuItems'] = $this->menuBuilder->getMenu()->getMenu()['items'];

        // Build posts list using unified config mapper and factory
        // Prepare attributes for async endpoint (REST API) to ensure all context is available
        $archiveAsyncAttributes = [
            ...$this->data,
            'wpTaxonomies' => $GLOBALS['wp_taxonomies'],
            'queryVarsPrefix' => 'archive_',
            'postType' => $this->data['postType'] ?? 'page',
            'customizer' => $this->data['customizer'] ?? [],
            'archiveProps' => $this->data['archiveProps'] ?? [],
        ];
        $postsListConfigDTO = (new \Municipio\PostsList\ConfigMapper\ArchiveDataToPostsListConfigMapper())->map($archiveAsyncAttributes);
        $postsListFactory = new \Municipio\PostsList\PostsListFactory(
            $this->wpService,
            $GLOBALS['wpdb'],
            new SchemaToPostTypeResolver($this->acfService, $this->wpService),
        );

        $postsList     = $postsListFactory->create($postsListConfigDTO);
        $postsListData = $postsList->getData();

        $postsListData['getAsyncAttributes'] = fn() => $this->getAsyncConfigFactory()->create(
            $postsListConfigDTO,
            $postsListData,
            true
        );

        $this->data = [
            ...$this->data,
            ...$postsListData,
        ];
    }

    /**
     * Get archive properties
     *
     * @param  string $postType
     * @param  array $customizer
     * @return array|bool
     */
    private function getArchiveProperties($postType, $customize)
    {
        $customizationKey = 'archive' . self::camelCasePostTypeName($postType);

        if (isset($customize->{$customizationKey})) {
            return (object) $customize->{$customizationKey};
        }

        return false;
    }

    /**
     * Convert post type name to camel case
     *
     * @param string $postType
     * @return string
     */
    private function camelCasePostTypeName(string $postType): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $postType)));
    }

    /**
     * Get the archive title
     *
     * @return string
     */
    protected function getArchiveTitle($args)
    {
        return (string) \apply_filters('Municipio/Controller/Archive/getArchiveTitle', $args->heading ?? '');
    }

    /**
     * Get the archive lead
     *
     * @return string
     */
    protected function getArchiveLead($args)
    {
        return (string) \apply_filters('Municipio/Controller/Archive/getArchiveLead', $args->body ?? '');
    }
}
