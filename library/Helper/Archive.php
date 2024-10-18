<?php

namespace Municipio\Helper;

/**
 * Archive class.
 *
 * This class is responsible for handling archive related functionality.
 * It is located in the file /workspaces/municipio-deployment/wp-content/themes/municipio/library/Helper/Archive.php.
 */
class Archive
{
    /**
     * Get archive properties
     * @param  string $postType
     * @param  array $customizer
     * @return array|bool
     */
    public static function getArchiveProperties(string $postType, object $customize)
    {
        $customizationKey = "archive" . self::camelCasePostTypeName($postType);

        if (isset($customize->{$customizationKey})) {
            return (object) $customize->{$customizationKey};
        }
        return false;
    }

    /**
     * Converts a post type name to camel case.
     *
     * @param string $postType The post type name to convert.
     * @return string The converted post type name in camel case.
     */
    public static function camelCasePostTypeName($postType)
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $postType)));
    }

    /**
     * Get the template style for this archive
     *
     * @param string $postType  The post type to get the option from
     * @param string $default   The default value, if not found.
     * @param string $postType  The post type to get the option from
     *
     * @return string
     */
    public static function getTemplate($args, string $default = 'cards', $postType = null): string
    {
        $schemaKey         = 'schema';
        $archiveAppearance = $default;

        if (empty($args->style)) {
            return $archiveAppearance;
        }

        $archiveAppearance = $args->style;

        if ($postType && $archiveAppearance === $schemaKey) {
            $schemaType = \Municipio\SchemaData\Helper\GetSchemaType::getSchemaTypeFromPostType($postType);

            if ($schemaType) {
                $archiveAppearance = $schemaKey . '-' . lcfirst($schemaType);
            }
        }

        return $archiveAppearance;
    }

    /**
     * Determines whether to show pagination for an archive page.
     *
     * @param string $archiveBaseUrl The base URL for the archive page.
     * @param int $maxNumPages The maximum number of pages for the archive.
     * @return bool Returns true if pagination should be shown, false otherwise.
     */
    public static function showPagination($archiveBaseUrl, $maxNumPages)
    {

        $pagesArray = self::getPagination($archiveBaseUrl, $maxNumPages);

        if (is_null($pagesArray)) {
            return false;
        }

        if (count($pagesArray) > 1) {
            return true;
        }

        return false;
    }

    /**
     * Get pagination
     *
     * @return array    Pagination array with label and link
     */
    public static function getPagination($archiveBaseUrl, $wpQuery)
    {
        $numberOfPages = (int) ceil($wpQuery->max_num_pages) + 1;

        if ($numberOfPages > 1) {
            for ($i = 1; $i < $numberOfPages; $i++) {
                $href = $archiveBaseUrl . '?' . self::setQueryString($i);

                $pagination[] = array(
                    'href'  => $href,
                    'label' => (string) $i
                );
            }
        }

        return \apply_filters('Municipio/Controller/Archive/getPagination', $pagination ?? null);
    }

    /**
    * Build a query string with page numer
    *
    * @param integer $number
    * @return void
    */
    public static function setQueryString($number)
    {
        $queryArgList = [];

        if (isset($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $queryArgList);
        }

        $queryArgList['paged'] = $number;
        $queryString           = http_build_query($queryArgList) . "\n";

        return \apply_filters('Municipio/Controller/Archive/setQueryString', $queryString);
    }

    /**
     * Boolean function to determine if navigation should be shown
     *
     * @param string $postType
     * @return boolean
     */
    public static function showFilter($args)
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

        return apply_filters('Municipio/Archive/showFilter', $enabledFilters, $args);
    }

    /**
     * Boolean function to determine if text search should be enabled
     *
     * @param   string      $postType   The current post type
     * @return  boolean                 True or false val.
     */
    public static function enableTextSearch($args)
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
    public static function enableDateFilter($args)
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
     * Retrieves the facetting type based on the provided arguments.
     *
     * @param object $args The arguments for retrieving the facetting type.
     * @return bool The facetting type.
     */
    public static function getFacettingType($args)
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
     * Display the reading time based on the provided arguments.
     *
     * @param object $args The arguments for displaying the reading time.
     * @return bool Returns true if the reading time is set in the arguments, false otherwise.
     */
    public static function displayReadingTime($args)
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
    public static function displayFeaturedImage($args)
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
     * Display the featured image on archive pages.
     *
     * @param array $args The arguments for displaying the featured image.
     * @return mixed The result of the displayFeaturedImage function.
     */
    public static function displayFeaturedImageOnArchive($args)
    {
        return self::displayFeaturedImage($args);
    }

    /**
     * Create a grid column size
     * @param  array $archiveProps
     * @return string
     */
    public static function getGridClass($args): string
    {
        $stack = [];

        if (!is_object($args)) {
            $args = (object) [];
        }

        if (!isset($args->numberOfColumns) || !is_numeric($args->numberOfColumns)) {
            $args->numberOfColumns = 4;
        }

        $stack[] = \Municipio\Helper\Html::createGridClass(1);

        if ($args->numberOfColumns == 2) {
            $stack[] = \Municipio\Helper\Html::createGridClass(2, 'md');
            $stack[] = \Municipio\Helper\Html::createGridClass(2, 'lg');
        }

        if ($args->numberOfColumns == 3) {
            $stack[] = \Municipio\Helper\Html::createGridClass(2, 'md');
            $stack[] = \Municipio\Helper\Html::createGridClass(3, 'lg');
        }

        if ($args->numberOfColumns == 4) {
            $stack[] = \Municipio\Helper\Html::createGridClass(2, 'sm');
            $stack[] = \Municipio\Helper\Html::createGridClass(3, 'md');
            $stack[] = \Municipio\Helper\Html::createGridClass(4, 'lg');
        }

        return implode(' ', $stack);
    }

    /**
     * Determines if the reset button should show or not.
     *
     * @return boolean
     */
    public static function showFilterReset($queryParams): bool
    {
        return !empty(array_filter(
            (array) $queryParams
        ));
    }
}
