<?php

namespace Modularity\Module\Rss;

class Rss extends \Modularity\Module
{
    public $slug = 'rss';
    public $supports = array();
    public $isBlockCompatible = false;

    public function init()
    {
        $this->nameSingular = __("RSS", 'modularity');
        $this->namePlural = __("RSS", 'modularity');
        $this->description = __("Outputs a RSS feed", 'modularity');
    }

    public function data() : array
    {
        $fields          = json_decode(json_encode(get_fields($this->ID)));
        $show_fields     = (isset($fields->fields) && ! empty($fields->fields)) ? $fields->fields : array();
        $data['summary'] = (int) in_array('summary', $show_fields);
        $data['author']  = (int) in_array('author', $show_fields);
        $data['date']    = (int) in_array('date', $show_fields);
        $data['items']   = $this->getFeedContents($fields->rss_url, $fields->items, $fields->sort_order);
        $data['classes'] = implode(' ', apply_filters('Modularity/Module/Classes', array('box', 'box-panel'), $this->post_type, $this->args));
        return $data;
    }

    /**
     * Returns the RSS entries
     * @param  string  $rss   RSS feed URL
     * @param  integer $items Number of items to return
     * @param  string  $order Sorting order, asc or desc
     * @return array          List of RSS entries
     */
    public function getFeedContents($rss, $items = 10, $order = '') : array
    {
        $entries = array();

        if (! empty($rss)) {
            $rss = fetch_feed($rss);
        } else {
            $entries['error'] = __('RSS feed URL is missing', 'modularity');
            return $entries;
        }

        if (is_wp_error($rss)) {
            if (is_admin() || current_user_can('manage_options')) {
                $entries['error'] = __('RSS Error:', 'modularity') . ' ' . $rss->get_error_message();
            }
            return $entries;
        }

        if (!$rss->get_item_quantity()) {
            $entries['error'] = __( 'An error has occurred, which probably means the feed is down. Try again later.', 'modularity');
            $rss->__destruct();
            unset($rss);
            return $entries;
        }

        $items = ($items <= 0) ? 0 : (int) $items;
        $entries = $rss->get_items(0, $items);
        if ($order == 'asc') {
            $entries = array_reverse($entries);
        }

        $rss->__destruct();
        unset($rss);
        return $entries;
    }

    /**
     * Sanitize title
     * @param  string $title default title
     * @return string        sanitized title
     */
    public static function getRssTitle($title = '')
    {
        $title = esc_html(trim(strip_tags($title)));
        $title = (empty($title)) ? __('Untitled', 'modularity') : $title;

        return $title;
    }

    /**
     * Sanitize link
     * @param  string $link default URL
     * @return string       sanitized URL
     */
    public static function getRssLink($link = '')
    {
        while (stristr($link, 'http') != $link) {
            $link = substr($link, 1);
        }
        $link = esc_url(strip_tags($link));

        return $link;
    }

    /**
     * Sanitize author name
     * @param  object $author object with author data
     * @return string         sanitized author name
     */
    public static function getRssAuthor($author)
    {
        if (is_object($author)) {
            $author = $author->get_name();
            $author = esc_html(strip_tags($author));
        } else {
            $author = '';
        }

        return $author;
    }

    /**
     * Sanitize and trim summary
     * @param  string $summary default RSS entry summary
     * @return string          sanitized entry
     */
    public static function getRssSummary($summary = '')
    {
        $summary = @html_entity_decode($summary, ENT_QUOTES, get_option('blog_charset'));
        $summary = esc_attr(wp_trim_words($summary, 50, ' [&hellip;]'));

        // Change existing [...] to [&hellip;].
        if ('[...]' == substr($summary, -5)) {
            $summary = substr($summary, 0, -5) . '[&hellip;]';
        }

        $summary = esc_html($summary);

        return $summary;
    }

    /**
     * Available "magic" methods for modules:
     * init()            What to do on initialization
     * data()            Use to send data to view (return array)
     * style()           Enqueue style only when module is used on page
     * script            Enqueue script only when module is used on page
     * adminEnqueue()    Enqueue scripts for the module edit/add page in admin
     * template()        Return the view template (blade) the module should use when displayed
     */
}
