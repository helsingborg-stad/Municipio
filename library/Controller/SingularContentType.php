<?php

namespace Municipio\Controller;

use Municipio\Helper\Data as DataHelper;
use Municipio\Helper\ContentType as ContentTypeHelper;
use Municipio\Helper\Term as TermHelper;
use Municipio\Helper\Listing as ListingHelper;

/**
 * Class SingularContentType
 * @package Municipio\Controller
 */
class SingularContentType extends \Municipio\Controller\Singular
{
    public $view;
    protected $contentType;

    public function __construct()
    {
        parent::__construct();

        $this->contentType = ContentTypeHelper::getContentType($this->data['post']->postType);

        // Set view if allowed
        if (!ContentTypeHelper::skipContentTypeTemplate($this->data['post']->postType)) {
            $this->view = $this->contentType->getView();
        }

    }

      /**
     * @return array|void
     */
    public function init()
    {
        parent::init();

        $post = \Municipio\Helper\ContentTypePlace::complementPlacePost($this->data['post'], false);

        $fields = get_fields($this->getPageID());

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
