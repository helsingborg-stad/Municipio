<?php

namespace Municipio\Controller;

use Municipio\Helper\ContentType;
use WP_Term;

/**
 * Class SingularContentType
 * @package Municipio\Controller
 */
class SingularContentType extends \Municipio\Controller\Singular
{
    /**
     * The view object.
     *
     * @var object
     */
    public $view;

    /**
     * The ID of the current post.
     *
     * @var int
     */
    protected $postId;

    /**
     * The content type object.
     *
     * @var object
     */
    protected $contentType;

    /**
     * SingularContentType constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->postId = $this->data['post']->id;

        /**
         * Retrieves the content type of the current post type.
         *
         * @return string The content type of the current post.
         */
        $contentType = ContentType::getPostTypeContentType($this->data['post']->postType);

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
         * If the content type has secondary content types, initiate hooks for each of them.
         *
         * @param object $contentType The content type object.
         * @return void
         */
        if (!empty($contentType->secondaryContentType)) {
            foreach ($contentType->secondaryContentType as $secondaryContentType) {
                $secondaryContentType->addHooks();
            }
        }

        /** Set Blade view */
        $this->view = $contentType->getView();
    }

    /**
     * Initiate the controller.
     *
     * @return array The data to send to the view.
     */
    public function init()
    {
        parent::init();

        // TODO Should related posts really be set here? They aren't technically dependent on the post having a content type. Figure out a better place to place this.
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
        $postTypes  = get_post_types(array('public' => true, '_builtin' => false), 'objects');

        $arr = [];
        foreach ($taxonomies as $taxonomy) {
            $terms = get_the_terms($postId, $taxonomy);
            if (!empty($terms)) {
                foreach ($terms as $term) {
                    if ($term instanceof WP_Term) {
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
                'numberposts'  => 3,
                'post_type'    => $postType->name,
                'post__not_in' => array($postId),
                'tax_query'    => array(
                    'relation' => 'OR',
                ),
            );

            foreach ($arr as $tax => $ids) {
                $args['tax_query'][] = array(
                    'taxonomy' => $tax,
                    'field'    => 'term_id',
                    'terms'    => $ids,
                    'operator' => 'IN',
                );
            }

            $result = get_posts($args);

            if (!empty($result)) {
                foreach ($result as &$post) {
                    $post                    = \Municipio\Helper\Post::preparePostObject($post);
                    $posts[$postType->label] = $result;
                }
            }
        }

        return $posts;
    }

    /**
     * Append structured data to the view data.
     *
     * @return string The structured data as a JSON string.
     */
    public function appendStructuredData(): string
    {
        $structuredData = [$this->contentType->getStructuredData($this->postId)];

        if (!empty($this->contentType->secondaryContentType)) {
            foreach ($this->contentType->secondaryContentType as $secondaryContentType) {
                $structuredData[] = $secondaryContentType->getStructuredData($this->postId);
            }
        }

        return \Municipio\Helper\Data::prepareStructuredData($structuredData);
    }
}
