<?php

namespace Modularity\Module\WpWidget;

class WpWidget extends \Modularity\Module
{
    public $slug = 'wpwidget';
    public $supports = array('editor');
    public $isBlockCompatible = false;

    public function init()
    {
        $this->nameSingular = __('Wordpress Widgets', 'modularity');
        $this->namePlural = __('Wordpress Widgets', 'modularity');
        $this->description = __('Outputs a default widget in WordPress');

        // Add options to list of selectable widgets
        add_filter('acf/load_field/name=mod_standard_widget_type', array($this, 'addWidgetOptionsList'));
        add_filter('acf/load_field/name=wp_widget_tag_cloud_taxonomy', array($this, 'tagCloudTaxonomies'));
    }

    public function data() : array
    {
        $data = $this->getFields();
        $data['settings'] = \Modularity\Module\WpWidget\WpWidget::createSettingsArray($data['mod_standard_widget_type'], $this->ID);
        $data['widgetBefore'] = apply_filters('Modularity/Module/WpWidget/before', '<div class="box">', $this->args, $this);
        $data['widgetAfter'] = apply_filters('Modularity/Module/WpWidget/after', '</div>', $this->args, $this);
        return $data;
    }

    /**
     * Displays the widget
     * @param  string $widget   The widget type
     * @param  array  $instance The widget instance
     * @return void
     */
    public static function displayWidget($widget, $instance = array())
    {
        if (array_key_exists($widget, \Modularity\Module\WpWidget\WpWidget::getWidgetIndexList()) && class_exists($widget)) {
            the_widget($widget, $instance);
        } else {
            echo "Error: Widget prohibited or class not found.";
        }
    }

    /**
     * Widget options list
     * @param array $field
     */
    public function addWidgetOptionsList($field)
    {
        $field['choices'] = $this->getWidgetIndexList();
        return $field;
    }

    /**
     * Add taxonomies to tag cloud widget form
     * @param  array $field
     * @return array
     */
    public function tagCloudTaxonomies($field)
    {
        $taxonomies = get_taxonomies(array('show_tagcloud' => true), 'object');

        foreach ($taxonomies as $key => $value) {
            $field['choices'][$key] = $value->labels->name;
        }

        return $field;
    }

    /**
     * Widget fields
     * @param  string $widget_class Widget class
     * @param  integer $post_id     Post id
     * @return arrat                Fields
     */
    public static function createSettingsArray($widget_class, $post_id)
    {
        switch ($widget_class) {
            case "WP_Widget_Archives":
                $settings = array(
                    'title' => get_the_title($post_id),
                    'count' => get_field('wp_widget_archive_count', $post_id),
                    'dropdown' => get_field('wp_widget_archive_dropdown', $post_id)
                );
                break;

            case "WP_Widget_Categories":
                $settings = array(
                    'title' => get_the_title($post_id),
                    'count' => get_field('wp_widget_cat_count', $post_id),
                    'hierarchical' => get_field('wp_widget_cat_hierarchical', $post_id),
                    'dropdown' => get_field('wp_widget_cat_dropdown', $post_id)
                );
                break;

            case "WP_Widget_Pages":
                $settings = array(
                    'title' => get_the_title($post_id),
                    'sortby' => get_field('wp_widget_pages_sort_by', $post_id),
                    'exclude' => get_field('wp_widget_pages_exclude', $post_id)
                );
                break;

            case "WP_Widget_Recent_Comments":
                $settings = array(
                    'title' => get_the_title($post_id),
                    'comments' => get_field('wp_widget_comments_comments', $post_id)
                );
                break;

            case "WP_Widget_Recent_Posts":
                $settings = array(
                    'title' => get_the_title($post_id),
                    'number' => get_field('wp_widget_posts_number', $post_id)
                );
                break;

            case "WP_Widget_RSS":
                $settings = array(
                    'title' => get_the_title($post_id),
                    'url' => get_field('wp_widget_rss_url', $post_id), //rss url atom/rss-xml,
                    'items' => get_field('wp_widget_rss_items', $post_id), //Max number of items to show
                    'show_summary' => get_field('wp_widget_rss_summary', $post_id),
                    'show_author' => get_field('wp_widget_rss_author', $post_id),
                    'show_date' => get_field('wp_widget_rss_date', $post_id)
                );
                break;

            case "WP_Widget_Tag_Cloud":
                $settings = array(
                    'title' => get_the_title($post_id),
                    'taxonomy' => get_field('wp_widget_tag_cloud_taxonomy', $post_id)
                );
                break;

            default:
                $settings = array(
                    'title' => get_the_title($post_id)
                );
        }

        //Allow modifications of the settings
        return apply_filters('Modularity/Module/WidgetSettings', $settings, $widget_class, $post_id);
    }

    public static function getWidgetIndexList()
    {
        //Valid names
        $widgetIndexList = array(
            'WP_Widget_Archives' => __("Archives", 'modularity'),
            'WP_Widget_Calendar' => __("Calendar", 'modularity'),
            'WP_Widget_Categories' => __("Categories", 'modularity'),
            'WP_Widget_Meta' => __("Meta", 'modularity'),
            'WP_Widget_Pages' => __("Pages", 'modularity'),
            'WP_Widget_Recent_Comments' => __("Recent comments", 'modularity'),
            'WP_Widget_Recent_Posts' => __("Recent posts", 'modularity'),
            'WP_Widget_RSS' => __("RSS", 'modularity'),
            'WP_Widget_Search' => __("Search", 'modularity'),
            'WP_Widget_Tag_Cloud' => __("Tag cloud", 'modularity')
        );

        //Allow modifications of the list
        return apply_filters('Modularity/Module/WidgetIndexList', $widgetIndexList);
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
