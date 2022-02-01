<?php

namespace Municipio;

class Language
{
    public function __construct()
    {
        add_filter('Municipio/Navigation/Item', array($this, 'addSourceUrl'), 10, 3);
    }

    /**
     * Adds the source url to language service menu items
     *
     * @param [type] $item
     * @param [type] $identifier
     * @param [type] $bool
     * @return void
     */
    public function addSourceUrl($item, $identifier, $bool) {

        //Check that we are in context of lang menu(s)
        if ($identifier != 'language') {
            return $item;
        }

        //Check that url is valid
        if (isset($item['href']) && filter_var($item['href'], FILTER_VALIDATE_URL) !== false) {
            //Get suitable param
            $queryParam = $this->getServiceQueryString($item['href']);

            //Add source url to query string
            if ($queryParam !== false) {
                $item['href'] = add_query_arg(
                    $queryParam,
                    $this->getCurrentUrl(),
                    $item['href']
                );
            }
        }

        return $item;
    }

    /**
     * Provides query string param for language service
     *
     * @param string $href
     * @return string|false
     */
    private function getServiceQueryString($href)
    {
        if (strpos($href, 'translate.google.com') !== false) {
            return 'u';
        }
        return false;
    }

    /**
     * Get the current pages url
     *
     * @return string
     */
    private function getCurrentUrl()
    {
        $prefix = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://");
        return urlencode($prefix . $_SERVER[HTTP_HOST] . $_SERVER[REQUEST_URI]);
    }
}
