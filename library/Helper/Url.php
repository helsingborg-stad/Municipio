<?php

namespace Municipio\Helper;

class Url
{
    /**
     * Get the url of the current page
     * @param  boolean $querystring Wheater to include querystring or not
     * @return string               Current url
     */
    public static function getCurrent($querystring = false)
    {
        $url =  '//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        if (!$querystring) {
            $url = preg_replace('/\?(.*)/', '', $url);
        }

        return $url;
    }

    /**
     * Exclude specified params from a querystring
     * @param  string       $queryString   The querystring
     * @param  array|string $excludeParams Paramters to exclude (remove)
     * @param  string       $suffix        Optional suffix to add last in the querystring
     * @return string                      The new querystring
     */
    public static function queryStringExclude($queryString, $excludeParams, $suffix = null)
    {
        $queryString = explode('&', $queryString);
        $query = array();

        foreach ($queryString as $value) {
            $parts = explode('=', $value);
            $query[$parts[0]] = isset($parts[1]) ? $parts[1] : '';
        }

        $queryString = $query;

        if (is_string($excludeParams)) {
            $excludeParams = array($excludeParams);
        }

        foreach ($excludeParams as $exclude) {
            unset($queryString[$exclude]);
        }

        $queryString = array_filter($queryString);
        $queryString = http_build_query($queryString);

        if (strlen($queryString) > 0) {
            $queryString .= $suffix;
        }

        return $queryString;
    }

    /**
     * Get array with querystring params and values in current url
     * @return array
     */
    public static function getQueryString()
    {
        $querystringsDefault = explode('&', $_SERVER['QUERY_STRING']);
        $querystrings = array();

        foreach ($querystringsDefault as $querystring) {
            $querystring = explode('=', $querystring);
            $querystrings[$querystring[0]] = isset($querystring[1]) ? $querystring[1] : null;
        }

        return $querystrings;
    }
}
