<?php

namespace Municipio\Controller;

use WP_Term;

/**
 * Class SingularContentType
 * @package Municipio\Controller
 */
class SingularContentType extends \Municipio\Controller\Singular
{
    public $view;
    protected $postId;
    protected $contentType;

    public function __construct()
    {
        parent::__construct();

        $this->postId = $this->data['post']->id;

        /**
         * Retrieves the content type of the current post typr.
         *
         * @return string The content type of the current post.
         */

        $postType = $this->data['post']->postType;

        $this->contentType = \Municipio\Helper\ContentType::getContentType($postType);

        // $currentContentType = new $contentType();
        $this->contentType->init();

        if(!empty($this->contentType->secondaryContentType)) {
            foreach($this->contentType->secondaryContentType as $secondaryContentType) {
                $secondaryContentType->init();
            }
        }

        /**
         * Check if the content type template should be skipped and set the view accordingly if not.
         */
        if (!\Municipio\Helper\ContentType::skipContentTypeTemplate($postType)) {
            $this->view = $this->contentType->getView();
        }

        $this->appendStructuredData();

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

        // $this->data['structuredData'] = \Municipio\Helper\Data::getStructuredData($this->data['post']->id);

        return $this->data;
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
                    if( $term instanceof WP_Term ) {
                        $arr[$taxonomy][] = $term->term_id;
                    }
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

    public function appendStructuredData()  {
        
        $structuredData = $this->contentType->getStructuredData($this->postId);

        // if(!empty($this->contentType->secondaryContentType)) {
        //     foreach($this->contentType->secondaryContentType as $secondaryContentType) {
        //         $structuredData = array_merge( 
        //             $structuredData,
        //             $secondaryContentType->getStructuredData($this->postId)
        //         );

        //     }
        // }

        $this->data['structuredData'] = \Municipio\Helper\Data::getStructuredData($structuredData ?? []);
    }
}