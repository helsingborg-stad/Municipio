<?php

namespace Municipio\Upgrade\Version;

class V20 implements \Municipio\Upgrade\VersionInterface
{
    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        $postTypes = $this->getAllPostTypes();

        //Translation sheme
        $scheme = [
            'title'                 => 'heading',
            'post_style'            => 'style',
            'number_of_posts'       => 'post_count',
            'sort_key'              => 'order_by',
            'sort_order'            => 'order_direction',
            'post_taxonomy_display' => 'taxonomies_to_display'
        ];

        if (is_array($postTypes) && !empty($postTypes)) {
            foreach ($postTypes as $postType) {
                $fromId = isset($postType->name) ?  'options_archive_' . $postType->name . '_' : false;
                $toId   = isset($postType->name) ?  'archive_' . $postType->name . '_' : false;

                if ($fromId != false) {
                    //Plain transfer according to scheme
                    foreach ($scheme as $oldKey => $newKey) {
                        set_theme_mod(
                            $toId . $newKey,
                            get_option($fromId . $oldKey) ?? null
                        );
                        delete_option($fromId . $oldKey); //Clean old option
                    }

                    //Move active filters
                    $filters = array_merge(
                        (array) get_option($fromId . 'feed_filtering_settings') ?? [],
                        (array) get_option($fromId . 'post_filters_header') ?? [],
                        (array) get_option($fromId . 'post_filters_sidebar') ?? []
                    );
                    set_theme_mod($toId . 'enabled_filters', array_filter($filters));
                    delete_option($fromId . 'feed_filtering_settings'); //Clean old option
                    delete_option($fromId . 'post_filters_header'); //Clean old option
                    delete_option($fromId . 'post_filters_sidebar'); //Clean old option

                    //Transfer columns
                    $columns = (int) preg_replace('/[^0-9]/', '', get_option($fromId . 'grid_columns')) ?? '4';
                    if (empty($columns)) {
                        $columns = '4';
                    }
                    set_theme_mod($toId . 'number_of_columns', (int) floor(12 / $columns));
                    delete_option($fromId . 'grid_columns'); //Clean old option
                }
            }
        }
    }

    /**
     * Get all post types
     *
     * @return array
     */
    private function getAllPostTypes()
    {
        $postTypes = array();
        foreach (get_post_types() as $key => $postType) {
            $args = get_post_type_object($postType);

            if (!$args->public || $args->name === 'page') {
                continue;
            }

            $postTypes[$postType] = $args;
        }

        $postTypes['author'] = (object) array(
            'name'              => 'author',
            'label'             => __('Author'),
            'has_archive'       => true,
            'is_author_archive' => true
        );

        return $postTypes;
    }
}