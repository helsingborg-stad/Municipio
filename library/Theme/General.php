<?php

namespace Municipio\Theme;

class General
{
    public function __construct()
    {
        add_filter('body_class', array($this, 'isChildTheme'));
        add_filter('body_class', array($this, 'e404classes'));

        add_filter('private_title_format', array($this, 'titleFormat'));
        add_filter('protected_title_format', array($this, 'titleFormat'));

        add_filter('accessibility_items', array($this, 'accessibilityItems'), 10, 1);
        add_filter('the_lead', array($this, 'theLead'));
        add_filter('the_content', array($this, 'removeEmptyPTag'));

        add_filter('acf/get_field_group', array($this, 'fixFieldgroupLocationPath'));

        add_filter('Modularity\Module\Sites\image_rendered', array($this, 'sitesGridImage'), 10, 2);

        remove_filter('template_redirect', 'redirect_canonical');
        //add_action('add_meta_boxes', array($this, 'removeDisplaySettings'), 9999);
    }

    /**
     * Remove display settings metabox from non public posttypes
     * @param  string $postType
     * @return void
     */
    public function removeDisplaySettings($postType)
    {
        $publicPostTypes = array_keys(\Municipio\Helper\PostType::getPublic());
        $publicPostTypes[] = 'page';

        if (!in_array($postType, $publicPostTypes)) {
            remove_meta_box('acf-group_56c33cf1470dc', $postType, 'side');
            remove_meta_box('group_56d83cff12bb3', $postType, 'side');
        }
    }

    public function e404classes($classes)
    {
        $classes[] = 'error404-2';
        return $classes;
    }

    /**
     * Returns image for module site grid
     * @param  string $image
     * @param  object $site
     * @return string
     */
    public function sitesGridImage($image, $site)
    {
        switch_to_blog($site->blog_id);

        $image = null;

        if ($frontpage = get_option('page_on_front') && get_the_post_thumbnail_url(get_option('page_on_front'))) {
            $src = get_the_post_thumbnail_url($frontpage);

            if ($src) {
                $image = '<div style="background-image:url(' . $src . ');" class="box-image">
                   <img alt="' . $site->blogname . '" src="' . $src . '">
                </div>';
            }
        }

        if (!$image && $logo = get_field('logotype_negative', 'option')) {
            $image = '<div class="box-image">
               ' . \Municipio\Helper\Svg::extract($logo['url']) . '
            </div>';
        }

        restore_current_blog();

        return $image;
    }

    /**
     * Fixes fieldgroups page-template path
     * @param  array $fieldgroup Fieldgroup
     * @return array
     */
    public function fixFieldgroupLocationPath($fieldgroup)
    {
        if (!isset($fieldgroup['location'])) {
            return $fieldgroup;
        }

        foreach ($fieldgroup['location'] as &$locations) {
            foreach ($locations as &$location) {
                if ($location['param'] !== 'page_template') {
                    return $fieldgroup;
                }

                $location['value'] = basename($location['value']);
            }
        }

        return $fieldgroup;
    }

    public function titleFormat($format)
    {
        return '%s';
    }

    /**
     * Creates a lead paragraph
     * @param  string $text Text
     * @return string       Markup
     */
    public function theLead($text)
    {
        return '<p class="lead">' . strip_shortcodes($text) . '</p>';
    }

    /**
     * Removes empty p-tags
     * @param  string $content Text
     * @return string       Markup
     */
    public function removeEmptyPTag($content)
    {
        $content    = force_balance_tags($content);
        $content    = preg_replace('#<p>\s*+(<br\s*/*>)?\s*</p>#i', '', $content);
        $content    = preg_replace('~\s?<p>(\s|&nbsp;)+</p>\s?~', '', $content);

        return $content;
    }

    /**
     * is-child-theme body class
     * @param  array  $classes Default classes
     * @return array           Modified calsses
     */
    public function isChildTheme($classes)
    {
        if (is_child_theme()) {
            $classes[] = "is-child-theme";
        }

        return $classes;
    }

    /**
     * Filter for adding accessibility items
     * @param  array $items Default item array
     * @return array        Modified item array
     */
    public function accessibilityItems($items)
    {
        $items[] = '<a href="#" onclick="window.print();return false;" class=""><i class="pricon pricon-print"></i> ' . __('Print', 'municipio') . '</a>';

        return $items;
    }
}
