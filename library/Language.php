<?php

namespace Municipio;

class Language
{
    /**
     * Initialize filters.
     */
    public function __construct()
    {
        add_filter('Municipio/Navigation/Item', array($this, 'addSourceUrl'), 10, 3);
        add_filter('Municipio/Navigation/Item', array($this, 'addLangAttribute'), 10, 3);
        add_filter('Municipio/Navigation/Item', array($this, 'addXfn'), 10, 3);
        add_filter('the_title', [$this, 'excludeTitleFromGoogleTranslate'], 10, 2);
        add_filter('the_content', [$this, 'excludeTitleFromGoogleTranslate']);
    }
/**
 * Exclude the title of the current post from Google Translate.
 *
 * @param string $filteredString The post title or content.
 * @return string The modified title/content or the original string.
 */
    public function excludeTitleFromGoogleTranslate($filteredString, $isTitle = false)
    {
        global $post;

        if (is_admin() || !is_a($post, 'WP_Post')) {
            return $filteredString;
        }

        $currentPostId      = $post->ID;
        $currentPostTitle   = $post->post_title;
        $currentPostContent = $post->post_content;

        $excludeTranslate = get_field('exclude_from_google_translate', $currentPostId);

        if ($excludeTranslate && $filteredString === $currentPostTitle && $isTitle) {
            return \Municipio\Helper\General::wrapStringInSpan($filteredString, ['translate' => 'no']);
        }

        // If this content is the content of the current post
        if ($excludeTranslate) {
            // in $currentPostContent, wrap all occurrences of $currentPostTitle with a span
            $pattern     = '/(?<!\w)' . preg_quote($currentPostTitle, '/') . '(?!\w)/u';
            $replacement = \Municipio\Helper\General::wrapStringInSpan($currentPostTitle, ['translate' => 'no']);
            return preg_replace($pattern, $replacement, $filteredString);
        }

        return $filteredString;
    }

    /**
     * Adds the source url to language service menu items
     *
     * @param array $item The menu item.
     * @param string $identifier The identifier for the menu.
     * @param bool $bool A boolean flag.
     * @return array The modified menu item.
     */
    public function addLangAttribute(array $item, string $identifier, bool $bool)
    {
        if ($identifier != 'language' || !isset($item['href'])) {
            return $item;
        }

        $url = parse_url($item['href']);

        if (!empty($url['query'])) {
            parse_str($url['query'], $query);

            if (isset($query['tl'])) {
                $item['attributeList']['lang'] = $query['tl'];
            }
        }

        return $item;
    }

    /**
     * Adds the source url to language service menu items
     *
     * @param array $item The menu item.
     * @param string $identifier The identifier for the menu.
     * @param bool $bool A boolean flag.
     * @return array The modified menu item.
     */
    public function addSourceUrl($item, $identifier, $bool)
    {
        if ($identifier != 'language') {
            return $item;
        }

        if (isset($item['href']) && filter_var($item['href'], FILTER_VALIDATE_URL) !== false) {
            $queryParam = $this->getServiceQueryString($item['href']);

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
     * Adds the source url to language service menu items
     *
     * @param array $item The menu item.
     * @param string $identifier The identifier for the menu.
     * @param bool $bool A boolean flag.
     * @return array The modified menu item.
     */
    public function addXfn(array $item, string $identifier, bool $bool)
    {
        if ($identifier != 'language') {
            return $item;
        }

        if (empty($item['xfn'])) {
            $item['xfn'] = 'nofollow';
        }

        return $item;
    }

    /**
     * Provides query string param for language service
     *
     * @param string $href The URL.
     * @return string|false The query string parameter or false.
     */
    private function getServiceQueryString($href)
    {
        if (strpos($href, 'translate.google.com') !== false) {
            return 'u';
        }
        return false;
    }

    /**
     * Get the current page's URL.
     *
     * @return string The current URL.
     */
    private function getCurrentUrl()
    {
        $prefix = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://");
        return urlencode($prefix . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    }
}
