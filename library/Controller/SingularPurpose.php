<?php

namespace Municipio\Controller;

use Municipio\Helper\Data as DataHelper;
use Municipio\Helper\Purpose as PurposeHelper;
use Municipio\Helper\Term as TermHelper;

/**
 * Class SingularPurpose
 * @package Municipio\Controller
 */
class SingularPurpose extends \Municipio\Controller\Singular
{
    public $view;
    public function __construct()
    {
        parent::__construct();

        $type = $this->data['post']->postType;

        /**
         * Instantiate the current main purpose
         */
        $purpose = PurposeHelper::getPurpose($type);
        if (!empty($purpose)) {
            // Run initialisation on the main purpose
            $instance = PurposeHelper::getPurposeInstance($purpose[0]->key, true);
            // Set view if allowed
            if (!PurposeHelper::skipPurposeTemplate($type)) {
                $this->view = $instance->getView();
            }
        }

        // STRUCTURED DATA (SCHEMA.ORG)
        $this->data['structuredData'] = DataHelper::getStructuredData(
            $this->data['postType'],
            $this->getPageID()
        );
    }

      /**
     * @return array|void
     */
    public function init()
    {
        parent::init();
        $fields = get_fields($this->getPageID());

        $this->data['list'][] = !empty($fields['location']) ? $this->createListItem($fields['location']['street_name'] . ' ' . $fields['location']['street_number'], 'location_on', $this->buildMapsLink($fields['location'])) : false;
        $this->data['list'][] = !empty($fields['phone']) ? $this->createListItem($fields['phone'], 'call') : false;
        $this->data['list'][] = !empty($fields['website']) ? $this->createListItem(__('Visit website', 'municipio'), 'language', $fields['website']) : false;

        $other = !empty($fields['other']) ? $this->getTermNames($fields['other']) : false;
        if (!empty($other)) {
            foreach ($other as $item) {
                $this->data['list'][] = $this->createListItem($item->name, $item->icon['src']);
            }
        }

        $this->data['labels'] = [
            'related' => __('Related', 'municipio'),
            'showAll' => __('Show all', 'municipio'),
        ];

        $postId = $this->data['post']->id;
        $this->data['relatedPosts'] = $this->getRelatedPosts($postId);

        return $this->data;
    }

    private function buildMapsLink($locations)
    {
        if (empty($locations) || empty($locations['lng']) || empty($locations['lat'])) {
            return false;
        }
        return 'https://www.google.com/maps/dir/?api=1&destination=' . $locations['lat'] . ',' . $locations['lng'] . '&travelmode=transit';
    }

    private function getRelatedPosts($postId)
    {
        $taxonomies = get_post_taxonomies($postId);
        $postTypes = get_post_types(array('public' => true, '_builtin' => false), 'objects');

        $arr = [];
        foreach ($taxonomies as $taxonomy) {
            $terms = get_the_terms($postId, $taxonomy);
            if (!empty($terms)) {
                foreach ($terms as $term) {
                    $arr[$taxonomy][] = $term->term_id;
                }
            }
        }

        if (empty($arr)) {
            return false;
        }

        $posts = [];
        foreach ($postTypes as $postType) {
            $args = array(
                'numberposts' => 3,
                'post_type' => $postType->name,
                'post__not_in' => array($postId),
                'tax_query' => array(
                    'relation' => 'OR',
                ),
            );

            foreach ($arr as $tax => $ids) {
                $args['tax_query'][] = array(
                    'taxonomy' => $tax,
                    'field' => 'term_id',
                    'terms' => $ids,
                    'operator' => 'IN',
                );
            }

            $result = get_posts($args);

            if (!empty($result)) {
                foreach ($result as &$post) {
                    $post = \Municipio\Helper\Post::preparePostObject($post);
                    $posts[$postType->label] = $result;
                }
            }
        }

        return $posts;
    }

    private function createListItem($label, $icon, $href = false)
    {
        if (!empty($label) && $label != " ") {
            return ['label' => $label, 'icon' => ['icon' => $icon, 'size' => 'md'], 'href' => $href];
        }

        return false;
    }

    private function getTermNames($termIds)
    {
        if (empty($termIds)) {
            return false;
        }

        $terms = array();
        foreach ($termIds as $termId) {
            $term = get_term($termId);
            if (!$term || is_wp_error($term)) {
                continue;
            }
            $term->icon = TermHelper::getTermIcon($term);
            $terms[] = $term;
        }
        return $terms;
    }
}
