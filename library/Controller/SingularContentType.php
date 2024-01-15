<?php

namespace Municipio\Controller;

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
        $this->contentType->addHooks();

        if (!empty($this->contentType->secondaryContentType)) {
            foreach ($this->contentType->secondaryContentType as $secondaryContentType) {
                $secondaryContentType->addHooks();
            }
        }

        /**
         * Check if the content type template should be skipped and set the view accordingly if not.
         */
        if (!\Municipio\Helper\ContentType::skipContentTypeTemplate($postType)) {
            $this->view = $this->contentType->getView();
        }

        $this->data['structuredData'] = $this->appendStructuredData();
    }

    /**
     * Initiate the controller.
     *
     * @return array The data to send to the view.
     */
    public function init()
    {
        parent::init();
    }

     /**
     * Append structured data to the view data.
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
