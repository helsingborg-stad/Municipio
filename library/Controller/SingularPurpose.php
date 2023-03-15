<?php

namespace Municipio\Controller;

use Municipio\Helper\Data as DataHelper;
use Municipio\Helper\Purpose as PurposeHelper;

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

        $this->data['phone'] = $fields['phone'];
        $this->data['website'] = $fields['website'];
        $this->data['location'] = $fields['location'];
        $this->data['cuisine'] = $this->getTermNames($fields['cuisine']);
        $this->data['other'] = $this->getTermNames($fields['other']);
        $this->data['activities'] = $this->getTermNames($fields['activities']);
        
        $this->data['guides'] = $this->getPosts('guide');

        $this->data['labels'] = [
            'relatedGuides' => __('Related guides & articles', 'municipio'),
            'similarPlaces' => __('Similar places', 'municipio'),
            'upcomingEvents' => __('Upcoming events', 'municipio'),
            'showAll' => __('Show all', 'municipio'),
        ];

        // var_dump($this->data['guides']);

        return $this->data;
    }

    private function getPosts($customPostType) {
        $args = [
            'post_type' => $customPostType,
        ];

        $posts = get_posts($args);

        if (empty($posts)) {
            return false;
        }

        foreach ($posts as &$post) {
            $post = \Municipio\Helper\Post::preparePostObject($post);
            $post->readingTime = \Municipio\Helper\ReadingTime::getReadingTime($post->postContent, 0, true);
        }
        var_dump($posts);
        return $posts;
    }

    private function getTermNames ($termIds) {
        if (empty($termIds)) {
            return false;
        }

        $terms = array();
        foreach ($termIds as $termId) {
            $terms[] = get_term($termId);
        }
        return $terms;
    }
}
