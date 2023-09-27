<?php

namespace Municipio\Controller;

use Municipio\Helper\Data;
use Municipio\Helper\ContentType;
use Municipio\Helper\Controller;

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

        $contentType = ContentType::getContentType($this->data['post']->postType);
        $this->data['contentType'] = $contentType;

        // Load controller specific to the content type
        $contentTypeController = Controller::locateController(Controller::camelCase($contentType->getKey()));
        $contentTypeControllerClass = Controller::getNamespace($contentTypeController) . '\\' . Controller::camelCase($contentType->getKey());
        
        require_once $contentTypeController;
        new $contentTypeControllerClass;

        // Set view if allowed
        if (!ContentType::skipContentTypeTemplate($this->data['post']->postType)) {
            $this->view = $contentType->getView();
        }

        // STRUCTURED DATA (SCHEMA.ORG)
        $this->data['structuredData'] = Data::getStructuredData(
            $this->data['post']->postType,
            $this->getPageID()
        );

    }

      /**
     * @return array|void
     */
    public function init()
    {
        parent::init();

        // TODO: This should only happen if the content type is in fact Place
        $post = \Municipio\Helper\ContentTypePlace::complementPlacePost($this->data['post'], false);

        $fields = get_fields($this->getPageID());

        // TODO: Should this be called on _all_ content types?
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
