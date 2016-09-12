<?php

namespace Municipio\Helper;

class ArchiveFilter
{
    public function __construct()
    {
        add_action('wp', array($this, 'initFilters'));
    }

    public function initFilters()
    {
        if ((!is_archive() && !is_post_type_archive() && !is_home()) || is_admin()) {
            return;
        }

        $taxonomies = $this->getActiveTaxonomies();

        add_action('HbgBlade/data', function ($data) use ($taxonomies) {
            $data['enabledHeaderFilters'] = get_field('archive_' . sanitize_title(get_post_type()) . '_post_filters_header', 'option');
            $data['enabledSidebarFilters'] = get_field('archive_' . sanitize_title(get_post_type()) . '_post_filters_sidebar', 'option');

            $data['filterTaxonomies'] = $taxonomies;

            return $data;
        });
    }

    public function getActiveTaxonomies()
    {
        $tax = array();
        $postType = get_post_type();
        $taxonomies = get_object_taxonomies($postType, 'object');

        foreach ($taxonomies as $key => $item) {
            $terms = get_terms($key, array(
                'hide_empty' => false
            ));

            $tax[$key] = array(
                'label' => $item->labels->name,
                'values' => $terms
            );
        }

        $tax = json_decode(json_encode($tax));
        return $tax;
    }
}
