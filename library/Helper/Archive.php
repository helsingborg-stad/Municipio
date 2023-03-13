<?php

namespace Municipio\Helper;

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
    public static function camelCasePostTypeName($postType)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $postType)));
    }
    /**
     * Get the template style for this archive
     *
     * @param string $postType  The post type to get the option from
     * @param string $default   The default value, if not found.
     *
     * @return string
     */
    public static function getTemplate($args, string $default = 'cards'): string
    {
        if (is_object($args) && isset($args->style) && !empty($args->style)) {
            return $args->style;
        }

        return $default;
    }

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
                    'href' => $href,
                    'label' => (string) $i
                );
            }
        }

        return \apply_filters('Municipio/Controller/Archive/getPagination', $pagination);
    }

    /**
    * Build a query string with page numer
    *
    * @param integer $number
    * @return void
    */
    public static function setQueryString($number)
    {
        parse_str($_SERVER['QUERY_STRING'], $queryArgList);
        $queryArgList['paged'] = $number;
        $queryString = http_build_query($queryArgList) . "\n";

        return \apply_filters('Municipio/Controller/Archive/setQueryString', $queryString);
    }
}
