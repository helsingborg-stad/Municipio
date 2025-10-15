<?php

namespace Modularity\Module\Index;

class Index extends \Modularity\Module
{
    public $slug = 'index';
    public $icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE2LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPg0KPCFET0NUWVBFIHN2ZyBQVUJMSUMgIi0vL1czQy8vRFREIFNWRyAxLjEvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkIj4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCINCgkgd2lkdGg9IjMxNS45NDRweCIgaGVpZ2h0PSIzMTUuOTQzcHgiIHZpZXdCb3g9IjAgMCAzMTUuOTQ0IDMxNS45NDMiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDMxNS45NDQgMzE1Ljk0MzsiDQoJIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGQ9Ik0xOTguNTg3LDIwNi43MDZjMC4yMzEsMC4wMzEsMC40NjQsMC4wNDcsMC42OTYsMC4wNDdjMC43NzQsMCwxLjUzOS0wLjE2OCwyLjI0Ni0wLjQ5bDExMS4yNTQtNTAuNzQ2DQoJCQljMi4zMDUtMS4wNDcsMy41NjQtMy41NTIsMy4wNDMtNi4wMzNjLTAuNTI3LTIuNDczLTIuNy00LjI1My01LjIzMS00LjI4MmMtMC45MDctMC4wMTEtOTAuOTg4LTEuOTI4LTEzMC45NjEtODEuODE1DQoJCQljLTMuNzc1LTcuNTM5LTEzLjA2Ny0xMi4wNDItMjEuMDk3LTEwLjExNUw2NS42NzUsNzUuNjQ2Yy00LjQ4MiwxLjA4MS04LjA1MiwzLjkzNy05Ljc5LDcuODMxDQoJCQljLTEuNzAxLDMuODA3LTEuNDYzLDguMjM0LDAuNjQ4LDEyLjE0OUM3Mi42NjMsMTI1LjU3OSwxMTguOCwxOTYuMzQ5LDE5OC41ODcsMjA2LjcwNnogTTY1LjczOSw4Ny44ODINCgkJCWMwLjM2NC0wLjgxLDEuMjYtMS40NDgsMi40NjMtMS43MzJsOTIuODY1LTIyLjM3M2MwLjQ1OS0wLjExMywwLjk0OS0wLjE2OSwxLjQ1LTAuMTY5YzIuOTg0LDAsNi4xMTcsMS45NDMsNy40NTYsNC42MTcNCgkJCWMzMC45ODcsNjEuOTIzLDg5LjgxNyw4MC4xOCwxMjAuNjA0LDg1LjU1NGwtOTIuMTIxLDQyLjAyMWMtNzMuOTE1LTEwLjQyMS0xMTcuMTkyLTc2Ljk5OC0xMzIuNDMtMTA1LjI5DQoJCQlDNjUuNjcsODkuODMxLDY1LjMyNSw4OC44MjEsNjUuNzM5LDg3Ljg4MnoiLz4NCgkJPHJlY3QgeT0iMjUxLjcyNiIgd2lkdGg9IjE5Ni4zMzEiIGhlaWdodD0iMTEuMzg1Ii8+DQoJCTxwb2x5Z29uIHBvaW50cz0iMzEwLjU0MiwyMTIuNzM5IDMxMC41NDIsMjAxLjM1OCAxOTkuMjYyLDI1MS43MjYgMTk5LjI2MiwyNjMuMTE2IAkJIi8+DQoJCTxwYXRoIGQ9Ik0zMTAuNTQyLDE5Ny4zODNoLTI4LjkzMWwtODMuMDE0LDM5LjYyNGwtMy42MTgsMC4wMTZjLTUwLjE5NSwwLTkwLjYyNC04Ljg4LTEyMC44NDktMTkuMzExTDAsMjQ3LjU4NmgxOTYuNjczDQoJCQlMMzEwLjU0MiwxOTcuMzgzeiIvPg0KCQk8cGF0aCBkPSJNMjEuMTQ2LDE4MS4zOThjMjEuNTc0LDEzLjYzMiw4MS45MjMsNDUuMzMxLDE3Ni4yMTcsNDQuNzg4bDEwOS44NzctNTIuNDQ1YzAsMC01LjkyMiwwLjAzMi0xNS42MjUtMC4yNTNsLTkzLjg5NCw0Mi44MzENCgkJCWwtMS41MzQtMC4yYy01My42ODctNi45NzItOTIuMDI3LTQxLjIxMi0xMTYuMTA1LTcxLjU0MmwtNTcuOTk3LDI2LjI1OUMxNi42NTQsMTczLjI5OSwxNi4xMDMsMTc4LjIxNCwyMS4xNDYsMTgxLjM5OHoiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8L3N2Zz4NCg==';
    public $hideTitle = true;
    public $supports = array();
    public $isDeprecated = true;

    public $thumbnailSize = array(400, 300);

    public function init()
    {
        $this->nameSingular = __('Index', 'modularity');
        $this->namePlural = __('Indexes', 'modularity');
        $this->description = __('Index card to use as an entry point for a "content section".', 'modularity');

        add_filter('acf/fields/post_object/query/key=field_569cf1252cfc9', array($this, 'postObjectQuery'), 10, 3);
    }

    /**
     * Get all data
     * @return array
     */
    public function data() : array
    {
        $data = $this->getFields();

        // Get column classes
        $data['columnClass'] = 'o-grid-6@md';
        if (!empty($data['index_columns'])) {
            $data['columnClass'] = $data['index_columns'];
        }

        $data['imageDimension'] = $this->getImageDimension($data['columnClass']);

        // Get the index items
        $data['items'] = $this->prepareItems($data['index']);

        return $data;
    }

    /**
     * Get additional data for index posts
     * @param  array $items Items in index
     * @return array        Updated items
     */
    public function prepareItems($items)
    {

        if (is_array($items) && !empty($items)) {
            foreach ($items as $key => &$item) {

                //Get linked post object.
                $postData = is_object($item['page']) ? $item['page'] : false;

                //Linking
                if ($item['link_type'] == 'external') {
                    $item['permalink'] = $item['link_url'];
                } elseif (is_object($postData) && isset($postData->ID) && $item['link_type'] == 'internal') {
                    $item['permalink'] = get_permalink($postData->ID);
                }

                //Thumbnail
                $item['thumbnail'] = $this->getThumbnail($item);

                if ($postData !== false && get_post_status($postData->ID)) {
                    //Retrive post content & lead
                    if (is_object($postData) && isset($postData->ID)) {
                        $item['title']          = $this->switchContent($item['title'], $postData->post_title);
                        $item['lead']           = preg_replace('#</?a(\s[^>]*)?>#i', '', $item['lead']);
                        $item['lead']           = $this->switchContent($item['lead'], $this->parseExcerpt($postData->post_content));
                    }

                } else {
                    unset($item);
                }

                //Removes empty/unfetchable objects from index
                if(isset($item) && $item['link_type'] == "internal" && empty($item['title']) && empty($item['lead']) && empty($item['thumbnail']) && empty($item['permalink'])) {
                    unset($items[$key]);
                }

            }
        }

        return $items;
    }

    /**
     * Parse the excerpt from the content.
     * @param  [string] $preferdValue       [The value preferd to output]
     * @return [string] $secondaryValue     [The secondary fallback value]
     */

    public function parseExcerpt($postContent)
    {
        if (strpos($postContent, "<!--more-->")) {
            return strip_tags(substr($postContent, 0, strpos($postContent, "<!--more-->")));
        }
        return wp_trim_words(strip_tags(apply_filters('get_the_excerpt', $postContent)), 55, "&hellip;");
    }

    /**
     * Enter a two value, if prefered value is empty. Use second value.
     * @param  [string] $preferdValue       [The value preferd to output]
     * @param [string] $secondaryValue     [The secondary fallback value]
     * @return [string] [One of the values above]
     */

    public function switchContent($preferdValue, $secondaryValue)
    {
        if (!empty($preferdValue)) {
            return $preferdValue;
        }
        return $secondaryValue;
    }

    /**
     * Get post thumbnail in correct size
     * @param  [type] $item [description]
     * @return [type]       [description]
     */
    public function getThumbnail($item)
    {
        if ($item['image_display'] == 'custom' || $item['link_type'] == 'external' || $item['link_type'] == 'unlinked' && !empty($item['custom_image']['ID'])) {
            return wp_get_attachment_image_src(
                $item['custom_image']['ID'],
                apply_filters(
                    'Modularity/index/image',
                    municipio_to_aspect_ratio('16:9', $this->thumbnailSize),
                    $this->args
                )
            );
        } elseif ($item['image_display'] == 'false') {
            return false;
        }

        return wp_get_attachment_image_src(
            get_post_thumbnail_id($item['page']->ID),
            apply_filters(
                'Modularity/index/image',
                municipio_to_aspect_ratio('16:9', $this->thumbnailSize),
                $this->args
            )
        );
    }

    /**
     * Get wanted image dimensions based on column class
     * @param  string $columnClass
     * @return array
     */
    public function getImageDimension($columnClass)
    {
        switch ($columnClass) {
            case "o-grid-12@md": // 1-col
                $this->thumbnailSize = array(1200, 900);
                return $this->thumbnailSize;

            case "o-grid-6@md": // 2-col
                $this->thumbnailSize = array(800, 600);
                return $this->thumbnailSize;

            default:
                $this->thumbnailSize = array(400, 300);
                return $this->thumbnailSize;
        }
    }

    /**
     * Return results from certain post types
     * @param  array $args    the WP_Query args used to find choices
     * @param  array $field   the field array containing all attributes & settings
     * @param  int   $post_id the current post ID being edited
     * @return array          updated WP_Query args
     */
    public function postObjectQuery($args, $field, $post_id)
    {
        $post_types = array('post', 'page');

        $custom_post_types = get_field('avabile_dynamic_post_types', 'option');
        if (is_array($custom_post_types) && !empty($custom_post_types)) {
            foreach ($custom_post_types as $post_type) {
                $post_types[] = sanitize_title(substr($post_type['post_type_name'], 0, 19));
            }
        }

        $args['post_type'] = $post_types;

        return $args;
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
