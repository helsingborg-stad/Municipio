<?php

namespace Municipio\Controller;

use Municipio\Controller\Navigation\Config\MenuConfig;
use Municipio\SchemaData\Utils\SchemaToPostTypesResolver\SchemaToPostTypeResolver;

/**
 * Class Archive
 *
 * @package Municipio\Controller
 */
class Archive extends \Municipio\Controller\BaseController
{
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
        $postsList = $postsListFactory->create($postsListConfigDTO);
        $postsListData = $postsList->getData();
        // Enable async loading for archives by providing minimal async attributes
        $queryVarsPrefix = $postsListConfigDTO->getQueryVarsPrefix() ?? 'archive_';
        $id = $postsListData['id'] ?? null;
        // Pass only minimal context for async endpoint to avoid long URLs
        // Always include a valid dateSource for async endpoint
        $appearanceConfig = $postsListConfigDTO->getAppearanceConfig();
        $getPostsConfig = $postsListConfigDTO->getGetPostsConfig();
        $filterConfig = $postsListConfigDTO->getFilterConfig();
        $dateSource = $appearanceConfig->getDateSource() ?? 'post_date';
        $dateFormat = method_exists($appearanceConfig, 'getDateFormat') && $appearanceConfig->getDateFormat() ? $appearanceConfig->getDateFormat()->value : 'date-time';
        $numberOfColumns = method_exists($appearanceConfig, 'getNumberOfColumns') ? $appearanceConfig->getNumberOfColumns() : 1;
        $postsPerPage = method_exists($getPostsConfig, 'getPostsPerPage') ? $getPostsConfig->getPostsPerPage() : 10;
        $paginationEnabled = method_exists($getPostsConfig, 'paginationEnabled') ? $getPostsConfig->paginationEnabled() : true;
        $asyncAttributes = [
            'queryVarsPrefix' => $archiveAsyncAttributes['queryVarsPrefix'],
            'id' => $postsListData['id'] ?? null,
            'postType' => $archiveAsyncAttributes['postType'],
            'dateSource' => $dateSource,
            'dateFormat' => $dateFormat,
            'numberOfColumns' => $numberOfColumns,
            'postsPerPage' => $postsPerPage,
            'paginationEnabled' => $paginationEnabled,
        ];
        $postsListData['getAsyncAttributes'] = fn() => $asyncAttributes;
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
