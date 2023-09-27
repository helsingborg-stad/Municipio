<?php

namespace Municipio\Controller;

use Municipio\Helper\ContentType as ContentTypeHelper;

/**
 * Class SingularContentType
 * @package Municipio\Controller
 */
class SingularContentType extends \Municipio\Controller\Singular
{
    public $view;

    public function __construct()
    {
        parent::__construct();

        $contentType = $this->getContentType($this->data['post']->postType);

        /**
         * Initiate hooks for the current content type.
         *
         * @param object $contentType The content type object.
         * @return void
         *
         * @since 1.0.0
         * @author Your Name
         */
        $contentType->addHooks();
        /**
         * If the content type has secondary content types, initate hooks for each of them.
         * 
         * @param object $contentType The content type object.
         * @return void
         */
        if(!empty($contentType->secondaryContentType)) {
            foreach ($contentType->secondaryContentType as $secondaryContentType) {
                $secondaryContentType->addHooks();
            }
        }

        // Set view if allowed
        if (!ContentTypeHelper::skipContentTypeTemplate($this->data['post']->postType)) {
            $this->view = $contentType->getView();
        }

    }

    /**
     * Initiate the controller.
     *
     * @return array The data to send to the view.
     */
    public function init()
    {
        parent::init();

        // TODO Should related posts really be set here? They aren't technically dependant on the post having a content type. Figure out a better place to place this.
        $this->data['relatedPosts'] = $this->getRelatedPosts($this->data['post']->id);

        return $this->data;
    }

    /**
     * Get the content type object for the current post type.
     *
     * @return object The content type object.
     */
    public function getContentType(): object
    {
        return ContentTypeHelper::getContentType($this->data['post']->postType);
    }

    
    /**
     * Get related posts based on the taxonomies of the current post.
     *
     * @param int $postId The ID of the current post.
     *
     * @return array|bool An array of related posts or false if no related posts are found.
     */
    // TODO Move this to a helper class
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
