<?php

namespace Municipio\Controller;

use Municipio\Helper\Data as DataHelper;
use Municipio\Helper\Purpose as PurposeHelper;
use Municipio\Helper\Term as TermHelper;
use Municipio\Helper\Listing as ListingHelper;

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

        $this->data['listing'] = [];

        // Listing items aviailable for all purposes:

        // Phone number
        if (!empty($fields['phone'])) {
            $this->data['listing']['phone'] = ListingHelper::createListingItem($fields['phone'], 'call');
        }

        // Website link (with fixed label)
        if (!empty($fields['website'])) {
            $this->data['listing']['website'] = ListingHelper::createListingItem(
                __('Visit website', 'municipio'),
                'language',
                $fields['website']
            );
        }

        // Apply filters to listing items
        $this->data['listing'] = apply_filters(
            'Municipio/Controller/SingularPurpose/listing',
            $this->data['listing'],
            $fields
        );

        $this->data['bookingLink'] = $fields['booking_link'] ?? false;

        $this->data['labels'] = (array) $this->data['lang'];
        $this->data['relatedPosts'] = $this->getRelatedPosts($this->data['post']->id);

        return $this->data;
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
}
