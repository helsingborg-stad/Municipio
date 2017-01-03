<?php

namespace Municipio\Theme;

class General
{
    public function __construct()
    {
        add_filter('body_class', array($this, 'colorScheme'));
        add_filter('body_class', array($this, 'isChildTheme'));

        add_filter('private_title_format', array($this, 'titleFormat'));
        add_filter('protected_title_format', array($this, 'titleFormat'));

        add_filter('the_lead', array($this, 'theLead'));
        add_filter('the_content', array($this, 'removeEmptyPTag'));

        add_filter('acf/get_field_group', array($this, 'fixFieldgroupLocationPath'));
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
        return '<p class="lead">' . $text . '</p>';
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
     * Color scheme body class
     * @param  array $classes Default classes
     * @return array          Modified classes
     */
    public function colorScheme($classes)
    {
        $color = get_field('color_scheme', 'option');

        if (!$color) {
            return $classes;
        }

        $classes['color_scheme'] = 'theme-' . $color;
        return $classes;
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
}
