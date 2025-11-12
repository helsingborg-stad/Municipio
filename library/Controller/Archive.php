<?php

namespace Municipio\Controller;

use Municipio\Controller\Navigation\Config\MenuConfig;
use Municipio\PostsList\Config\AppearanceConfig\DefaultAppearanceConfig;
use Municipio\PostsList\Config\AppearanceConfig\PostDesign;
use Municipio\PostsList\Config\FilterConfig\DefaultFilterConfig;
use Municipio\PostsList\Config\GetPostsConfig\OrderDirection;
use Municipio\PostsList\GetPosts\WpQueryFactory;
use Municipio\PostsList\QueryVarRegistrar\QueryVarRegistrar;
use WP_Taxonomy;
use WP_Term;

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
        $this->data['archiveTitle']    = $this->getArchiveTitle($this->data['archiveProps']);
        $this->data['archiveLead']     = $this->getArchiveLead($this->data['archiveProps']);
        $this->data['archiveBaseUrl']  = $this->getPostTypeArchiveLink($postType);
        $this->data['archiveResetUrl'] = $this->getPostTypeArchiveLink($postType);

        // Build archive menu
        $archiveMenuConfig = new MenuConfig('archive-menu', $postType . '-menu');
        $this->menuBuilder->setConfig($archiveMenuConfig);
        $this->menuDirector->buildStandardMenu();
        $this->data['archiveMenuItems'] = $this->menuBuilder->getMenu()->getMenu()['items'];

        // Build posts list
        $this->data = [...$this->data, ...(new \Municipio\PostsList\PostsList(
            $this->createGetPostConfig(),
            $this->createAppearanceConfig(),
            $this->createFilterConfig(),
            $this->getAllRegisteredTaxonomies(),
            new WpQueryFactory(),
            new \Municipio\PostsList\QueryVars\QueryVars('archive_'),
            $this->wpService,
        ))->getData()];
    }

    /**
     * Get all registered taxonomies
     *
     * @return WP_Taxonomy[]
     */
    private function getAllRegisteredTaxonomies(): array
    {
        global $wp_taxonomies;

        return $wp_taxonomies;
    }

    /**
     * Get the post type for the archive
     *
     * @return string
     */
    private function getPostType(): string
    {
        return !empty($this->data['postType']) ? $this->data['postType'] : 'page';
    }

    /**
     * Get filter configuration based on archive properties
     *
     * @return \Municipio\PostsList\Config\FilterConfig\FilterConfigInterface
     */
    private function createFilterConfig(): \Municipio\PostsList\Config\FilterConfig\FilterConfigInterface
    {
        $isEnabled           = $this->showFilter($this->data['archiveProps']);
        $resetUrl            = $this->getPostTypeArchiveLink($this->getPostType());
        $isDateFilterEnabled = $this->enableDateFilter($this->data['archiveProps']);
        $isTextSearchEnabled = $this->enableTextSearch($this->data['archiveProps']);
        $taxonomies          = $this->getFilterTaxonomiesFromSettings((array) $this->data['archiveProps']);

        return new class ($isEnabled, $resetUrl, $isDateFilterEnabled, $isTextSearchEnabled, $taxonomies) extends DefaultFilterConfig {
            /**
             * Constructor
             */
            public function __construct(
                private bool $isEnabled,
                private string $resetUrl,
                private bool $isDateFilterEnabled,
                private bool $isTextSearchEnabled,
                private array $taxonomies
            ) {
            }

            /**
             * @inheritDoc
             */
            public function isEnabled(): bool
            {
                return $this->isEnabled;
            }

            /**
             * @inheritDoc
             */
            public function isTextSearchEnabled(): bool
            {
                return $this->isTextSearchEnabled;
            }

            /**
             * @inheritDoc
             */
            public function getResetUrl(): ?string
            {
                return $this->resetUrl;
            }

            /**
             * @inheritDoc
             */
            public function isDateFilterEnabled(): bool
            {
                return $this->isDateFilterEnabled;
            }

            /**
             * @inheritDoc
             */
            public function getTaxonomiesEnabledForFiltering(): array
            {
                return $this->taxonomies;
            }
        };
    }

    /**
     * Get post configuration based on archive properties
     *
     * @return \Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface
     */
    private function createGetPostConfig(): \Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface
    {
        $postType           = [$this->getPostType()];
        $isFacettingEnabled = $this->getFacettingType($this->data['archiveProps']);
        $orderBy            = $this->data['archiveProps']->orderBy ?? 'post_date';
        $perPage            = (int)get_theme_mod('archive_' . $this->getPostType() . '_post_count', 12);
        $dateSource         = $this->data['archiveProps']->dateField;
        $order              = $this->data['archiveProps']->orderDirection && strtoupper($this->data['archiveProps']->orderDirection) === 'ASC'
            ? OrderDirection::ASC
            : OrderDirection::DESC;

        return new class (
            $postType,
            $isFacettingEnabled,
            $orderBy,
            $order,
            $perPage,
            $dateSource
        ) extends \Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig {
            /**
             * Constructor
             */
            public function __construct(
                private array $postType,
                private bool $isFacettingEnabled,
                private string $orderBy,
                private OrderDirection $order,
                private int $perPage,
                private string $dateSource
            ) {
            }

            /**
             * @inheritDoc
             */
            public function getPostTypes(): array
            {
                return $this->postType;
            }

            /**
             * @inheritDoc
             */
            public function getPostsPerPage(): int
            {
                return $this->perPage;
            }

            /**
             * @inheritDoc
             */
            public function isFacettingTaxonomyQueryEnabled(): bool
            {
                return $this->isFacettingEnabled;
            }

            /**
             * @inheritDoc
             */
            public function getOrderBy(): string
            {
                return $this->orderBy;
            }

            /**
             * @inheritDoc
             */
            public function getOrder(): \Municipio\PostsList\Config\GetPostsConfig\OrderDirection
            {
                return $this->order;
            }

            public function getDateSource(): string
            {
                return $this->dateSource;
            }
        };
    }

    /**
     * Get appearance configuration based on archive properties
     *
     * @return \Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface
     */
    private function createAppearanceConfig(): \Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface
    {
        $numberOfColumns            = $this->data['archiveProps']->numberOfColumns ?? 1;
        $shouldDisplayFeaturedImage = $this->displayFeaturedImage($this->data['archiveProps']);
        $shouldDisplayReadingTime   = $this->displayReadingTime($this->data['archiveProps']);
        $taxonomiesToDisplay        = $this->data['archiveProps']->taxonomiesToDisplay ?: [];
        $postPropertiesToDisplay    = $this->data['archiveProps']->postPropertiesToDisplay ?? [];
        $template                   = $this->data['archiveProps']->style ?? 'cards';
        $design                     = match ($template) {
            'cards' => PostDesign::CARD,
            'compressed' => PostDesign::COMPRESSED,
            'collection' => PostDesign::COLLECTION,
            'grid' => PostDesign::BLOCK,
            'newsitem' => PostDesign::NEWSITEM,
            'schema' => PostDesign::SCHEMA,
            'list' => PostDesign::TABLE,
            default => PostDesign::CARD,
        };
        return new class (
            $numberOfColumns,
            $shouldDisplayFeaturedImage,
            $shouldDisplayReadingTime,
            $taxonomiesToDisplay,
            $postPropertiesToDisplay,
            $design
        ) extends DefaultAppearanceConfig {
            /**
             * Constructor
             */
            public function __construct(
                private int $numberOfColumns,
                private bool $shouldDisplayFeaturedImage,
                private bool $shouldDisplayReadingTime,
                private array $taxonomiesToDisplay,
                private array $postPropertiesToDisplay,
                private PostDesign $design
            ) {
            }

            /**
             * @inheritDoc
             */
            public function getDesign(): PostDesign
            {
                return $this->design;
            }

            /**
             * @inheritDoc
             */
            public function shouldDisplayFeaturedImage(): bool
            {
                return $this->shouldDisplayFeaturedImage;
            }

            /**
             * @inheritDoc
             */
            public function shouldDisplayReadingTime(): bool
            {
                return $this->shouldDisplayReadingTime;
            }

            /**
             * @inheritDoc
             */
            public function getTaxonomiesToDisplay(): array
            {
                return $this->taxonomiesToDisplay;
            }

            /**
             * @inheritDoc
             */
            public function getPostPropertiesToDisplay(): array
            {
                return $this->postPropertiesToDisplay;
            }

            /**
             * @inheritDoc
             */
            public function getNumberOfColumns(): int
            {
                return $this->numberOfColumns;
            }
        };
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
        $customizationKey = "archive" . self::camelCasePostTypeName($postType);

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
     * Determines if the filter should be shown.
     *
     * @param object $args
     * @return boolean
     */
    public function showFilter($args): bool
    {
        $enabledFilters = false;

        if (!is_object($args)) {
            $args = (object) [];
        }

        $arrayWithoutEmptyValues = isset($args->enabledFilters)
            ? array_filter($args->enabledFilters, fn($element) => !empty($element))
            : [];

        if (!empty($arrayWithoutEmptyValues)) {
            $enabledFilters = $args->enabledFilters;
        }

        $enabledFilters = apply_filters('Municipio/Archive/showFilter', $enabledFilters, $args);

        return is_array($enabledFilters) && !empty($enabledFilters) ? true : (bool) $enabledFilters;
    }

    /**
     * Boolean function to determine if text search should be enabled
     *
     * @param   string      $postType   The current post type
     * @return  boolean                 True or false val.
     */
    public function enableTextSearch($args)
    {
        if (!is_object($args)) {
            $args = (object) [];
        }

        return (bool) in_array(
            'text_search',
            isset($args->enabledFilters) && is_array($args->enabledFilters) ? $args->enabledFilters : []
        );
    }

    /**
     * Boolean function to determine if date filter should be enabled
     *
     * @param   string      $postType   The current post type
     * @return  boolean                 True or false val.
     */
    public function enableDateFilter($args)
    {
        if (!is_object($args)) {
            $args = (object) [];
        }

        return (bool) in_array(
            'date_range',
            isset($args->enabledFilters) && is_array($args->enabledFilters) ? $args->enabledFilters : []
        );
    }

    /**
     * Get the link to this page, without any query parameters
     *
     * @param   string  $postType   The current post type
     *
     * @return string
     */
    public function getPostTypeArchiveLink($postType)
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            $realPath      = (string) parse_url(home_url() . $_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $postTypePath  = (string) parse_url(get_post_type_archive_link($postType), PHP_URL_PATH);
            $mayBeTaxonomy = (bool)   ($realPath != $postTypePath);

            if ($mayBeTaxonomy && is_a(get_queried_object(), 'WP_Term')) {
                return get_term_link(get_queried_object());
            }
        }

        return get_post_type_archive_link($postType);
    }

    /**
     * Determines if the date input toggle should default to show or not.
     *
     * @return boolean
     */
    public function showDatePickers($queryParams): bool
    {
        //From field
        if (isset($queryParams->from) && !empty($queryParams->from)) {
            return true;
        }

        //To field
        if (isset($queryParams->to) && !empty($queryParams->to)) {
            return true;
        }

        return false;
    }

    /**
     * Get the archive title
     *
     * @return string
     */
    protected function getArchiveTitle($args)
    {
        return (string) \apply_filters(
            'Municipio/Controller/Archive/getArchiveTitle',
            $args->heading ?? ''
        );
    }

    /**
     * Get the archive lead
     *
     * @return string
     */
    protected function getArchiveLead($args)
    {
        return (string) \apply_filters(
            'Municipio/Controller/Archive/getArchiveLead',
            $args->body ?? ''
        );
    }

    /**
     * Boolean function to determine if text search should be enabled
     *
     * @param   string      $postType   The current post type
     * @return  boolean                 True or false val.
     */
    public function getFacettingType($args): bool
    {
        if (!is_object($args)) {
            $args = (object) [];
        }

        if (!isset($args->filterType) || is_null($args->filterType)) {
            $args->filterType = false;
        }

        return (bool) $args->filterType;
    }

    /**
     * Determines whether to display the reading time for an archive.
     *
     * @param array $args The arguments for displaying the reading time.
     * @return bool Returns true if the reading time is set in the arguments, false otherwise.
     */
    public function displayReadingTime($args)
    {
        if (!is_object($args)) {
            $args = (object) [];
        }

        if (!isset($args->readingTime)) {
            return false;
        }

        return (bool) $args->readingTime;
    }

    /**
     * Display the featured image based on the provided arguments.
     *
     * @param object $args The arguments for displaying the featured image.
     * @return bool Returns true if the featured image should be displayed, false otherwise.
     */
    public function displayFeaturedImage($args)
    {
        if (!is_object($args)) {
            $args = (object) [];
        }

        if (!isset($args->displayFeaturedImage)) {
            return false;
        }

        return (bool) $args->displayFeaturedImage;
    }

    /**
     * Get taxonomies to use for filtering from settings
     *
     * @param array $settings
     * @return array
     */
    private function getFilterTaxonomiesFromSettings(array $settings): array
    {
        $taxonomies = apply_filters('Municipio/Archive/getTaxonomyFilters/taxonomies', array_diff(
            $settings['enabledFilters'] ?? [],
            [$this->currentTaxonomy()]
        ), $this->currentTaxonomy());

        if (empty($taxonomies)) {
            return [];
        }

        // Wash out invalid taxonomies
        $allTaxonomies = $this->wpService->getTaxonomies([], 'names');
        $taxonomies    = array_intersect($allTaxonomies, $taxonomies);

        return array_values($taxonomies);
    }


    /**
     * Get the current taxonomy page
     */
    private function currentTaxonomy()
    {
        $queriedObject = get_queried_object();
        $isTaxArchive  = false;
        if (!empty($queriedObject->taxonomy) && isset($_SERVER['REQUEST_URI'])) {
            $pathParts   = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
            $trimmedPath = end($pathParts);
            if ($queriedObject->slug == $trimmedPath) {
                $isTaxArchive = $queriedObject->taxonomy;
            }
        }
        return $isTaxArchive;
    }
}
