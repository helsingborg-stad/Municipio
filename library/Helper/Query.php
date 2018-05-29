<?php

namespace Municipio\Helper;

class Query
{
    /**
     * Get pagination data from main query
     * @return array Pagination data
     */
    public static function getPaginationData()
    {
        global $wp_query;

        $data = array();

        $data['postType'] = $wp_query->query['post_type'];
        $data['postCount'] = $wp_query->post_count;
        $data['postTotal'] = $wp_query->found_posts;
        $data['pageIndex'] = ($wp_query->query['paged']) ? intval($wp_query->query['paged']) : 1;
        $data['pageTotal'] = $wp_query->max_num_pages;

        return $data;
    }

    /**
     * Get terms from main query (if tax_query)
     * @return array Terms
     */
    public static function getTaxQueryTerms()
    {
        if (!get_query_var('tax_query') || !is_array(get_query_var('tax_query')) || empty(get_query_var('tax_query'))) {
            return false;
        }

        $taxonomies = array();
        foreach (get_query_var('tax_query') as $key => $query) {
            if (is_numeric($key)) {
                $taxonomies[$query['taxonomy']] = $query['terms'];
            }
        }

        if (!isset($taxonomies) || !is_array($taxonomies) || empty($taxonomies)) {
            return false;
        }

        $terms = array();
        foreach ($taxonomies as $taxonomy => $taxTerm) {
            foreach ($taxTerm as $slug) {
                $terms[] = get_term_by('slug', $slug, $taxonomy);
            }
        }

        if (!isset($terms) || !is_array($terms) || empty($terms)) {
            return false;
        }

        return $terms;
    }
}
