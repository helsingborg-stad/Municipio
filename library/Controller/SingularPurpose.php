<?php

namespace Municipio\Controller;

use Municipio\Helper\Location;
use Municipio\Helper\PurposeHelper;

class SingularPurpose extends \Municipio\Controller\Singular
{
    public $type;

    public function init()
    {
        add_filter(
            'Municipio/Controller/Singular/getSingularPosts',
            [Location::class, 'addLocationDataToPosts'],
            10,
            1
        );

        parent::init();

        $this->type = get_post_type(get_queried_object_id());

        $this->setupOpenStreetMap();
    }

    private function setupOpenStreetMap()
    {
        $this->data['displayMap'] = in_array('singular', PurposeHelper::purposeMapLocation($this->type), true);

        if ($this->data['displayMap']) {
            $displayGoogleMapsLink = PurposeHelper::purposeMapDisplayGoogleMapsLink($this->type);

            $this->data['pins'] = Location::createPins([$this->data['post']], $displayGoogleMapsLink);
            $this->data['postsWithLocation'] = Location::filterPostsWithLocationData([$this->data['post']]);
        } else {
            $this->data['pins'] = [];
            $this->data['postsWithLocation'] = [];
        }
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
