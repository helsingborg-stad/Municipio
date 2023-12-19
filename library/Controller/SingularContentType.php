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

    public function __construct()
    {
        parent::__construct();

        /**
         * Retrieves the content type of the current post typr.
         *
         * @return string The content type of the current post.
         */
        $contentType = \Municipio\Helper\ContentType::getContentType($this->data['post']->postType);

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

        /**
         * Check if the content type template should be skipped and set the view accordingly if not.
         */
        if (!\Municipio\Helper\ContentType::skipContentTypeTemplate($this->data['post']->postType)) {
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
        return $this->data;
    }

}
