<?php

namespace Municipio\Controller;

use Municipio\Archive\AsyncAttributesProvider\ArchiveAsyncAttributesProvider;
use Municipio\Archive\AsyncAttributesProvider\AsyncAttributesProviderInterface;
use Municipio\Controller\Navigation\Config\MenuConfig;
use Municipio\SchemaData\Utils\SchemaToPostTypesResolver\SchemaToPostTypeResolver;

/**
 * Class Archive
 *
 * @package Municipio\Controller
 */
class Archive extends \Municipio\Controller\BaseController
{
    private ?AsyncAttributesProviderInterface $asyncAttributesProvider = null;
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

        // Initialize async attributes provider
        $this->asyncAttributesProvider = new ArchiveAsyncAttributesProvider(
            $postType,
            $this->data['archiveProps']
        );

        //Archive data
        $this->data['archiveTitle'] = $this->getArchiveTitle($this->data['archiveProps']);
        $this->data['archiveLead'] = $this->getArchiveLead($this->data['archiveProps']);

        // Build archive menu
        $archiveMenuConfig = new MenuConfig('archive-menu', $postType . '-menu');
        $this->menuBuilder->setConfig($archiveMenuConfig);
        $this->menuDirector->buildStandardMenu();
        $this->data['archiveMenuItems'] = $this->menuBuilder->getMenu()->getMenu()['items'];

        // Build posts list using unified config mapper and factory
        $postsListConfigDTO = (new \Municipio\PostsList\ConfigMapper\ArchiveDataToPostsListConfigMapper())->map([
            ...$this->data,
            'wpTaxonomies' => $GLOBALS['wp_taxonomies'],
        ]);
        $postsListFactory = new \Municipio\PostsList\PostsListFactory(
            $this->wpService,
            $GLOBALS['wpdb'],
            new SchemaToPostTypeResolver($this->acfService, $this->wpService),
        );
        $postsList = $postsListFactory->create($postsListConfigDTO);

        $this->data = [
            ...$this->data,
            ...$postsList->getData(),
        ];

        // Override getAsyncAttributes after PostsList data merge
        $this->data['getAsyncAttributes'] = fn() => $this->getAsyncAttributes();
    }

    /**
     * Get archive properties
     *
     * @param  string $postType
     * @param  array $customizer
     * @return object Always returns an object, empty if no customization exists
     */
    private function getArchiveProperties($postType, $customize): object
    {
        $customizationKey = 'archive' . self::camelCasePostTypeName($postType);

        if (isset($customize->{$customizationKey})) {
            return (object) $customize->{$customizationKey};
        }

        return (object) [];
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
     * @param object $args Archive properties object
     * @return string
     */
    protected function getArchiveTitle(object $args): string
    {
        return (string) \apply_filters('Municipio/Controller/Archive/getArchiveTitle', $args->heading ?? '');
    }

    /**
     * Get the archive lead
     *
     * @param object $args Archive properties object
     * @return string
     */
    protected function getArchiveLead(object $args): string
    {
        return (string) \apply_filters('Municipio/Controller/Archive/getArchiveLead', $args->body ?? '');
    }

    /**
     * Get async attributes for the archive
     *
     * Delegates to the AsyncAttributesProvider to get JSON-serializable
     * attributes for async rendering and client-side hydration.
     *
     * @return array
     */
    protected function getAsyncAttributes(): array
    {
        return $this->asyncAttributesProvider?->getAttributes() ?? [];
    }
}
