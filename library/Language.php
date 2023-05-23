<?php

namespace Municipio;

class Language
{
    public function __construct()
    {
        add_filter('Municipio/Navigation/Item', array($this, 'addSourceUrl'), 10, 3);

        add_filter('the_title', [$this, 'excludeTitleFromGoogleTranslate'], 10, 1);
        add_filter('the_content', [$this, 'exludeStringFromGoogleTranslate'], 10, 1);
    }

    /**
 * Exclude the title from Google Translate if a certain field is set.
 *
 * @param string $title The title to process.
 * @return string The processed title.
 */
    public function excludeTitleFromGoogleTranslate($title)
    {
        if (!get_field('exclude_from_google_translate')) {
            return $title;
        }

        return \Municipio\Helper\General::wrapStringInSpan($title, ['translate' => 'no']);
    }

/**
 * Exclude a string from Google Translate if a certain field is set.
 *
 * @param string $content The content to process.
 * @return string The processed content.
 */
    public function exludeStringFromGoogleTranslate($content)
    {
        if (!get_field('exclude_from_google_translate')) {
            return $content;
        }

        $matches = [];
        // Find all instances of $string in $content and return as an array
        preg_match_all("/$content/", $content, $matches);

        // Wrap each match in a <span> tag with the "translate" attribute set to "no"
        $wrappedMatches = array_map(fn($match) =>
        \Municipio\Helper\General::wrapStringInSpan($match, ['translate' => 'no']), $matches[0]);

        // Replace each match in $content with its wrapped version
        $content = str_replace($matches[0], $wrappedMatches, $content);

        return $content;
    }

    /**
     * Adds the source url to language service menu items
     *
     * @param [type] $item
     * @param [type] $identifier
     * @param [type] $bool
     * @return void
     */
    public function addSourceUrl($item, $identifier, $bool)
    {

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
        return urlencode($prefix . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    }
}
