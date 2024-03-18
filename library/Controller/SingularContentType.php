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

    /**
     * SingularContentType construct
     */
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

        $this->setContentTypeViewData();

        $this->contentType->addHooks();

        if (!empty($this->contentType->getSecondaryContentType())) {
            foreach ($this->contentType->getSecondaryContentType() as $secondaryContentType) {
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
    public function appendStructuredData(): ?string
    {
        return \Municipio\Helper\Data::normalizeStructuredData(
            $this->contentType->getStructuredData(
                $this->postId
            )
        );
    }
    /**
     * Set up view data based on the content type of the current post.
     * @return void
     */
    private function setContentTypeViewData(): void
    {
        $contentType = $this->contentType->getKey();

        if (empty($contentType)) {
            return;
        }

        // Handle specific content types
        switch ($contentType) {
            case 'place':
                $this->data['post'] = \Municipio\Helper\ContentType::complementPlacePost($this->data['post']);
                break;
        }
    }

}
