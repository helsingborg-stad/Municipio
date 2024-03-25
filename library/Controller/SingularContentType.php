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
        $structuredData = apply_filters('Municipio/preStructuredData', [], $this->postId);

        if(empty($structuredData) ) {
            $structuredData = $this->contentType->getStructuredData($this->postId);
        }

        if(!empty($structuredData)) {
            if(!empty($structuredData['description'])) {
              $structuredData['description'] = wp_strip_all_tags($structuredData['description']);
            }
            return \Municipio\Helper\Data::normalizeStructuredData($structuredData);
        }
    }

     /**
     * Set up view data based on the content type of the current post.
     *
     * If the content type is "place," the post data is complemented using
     * \Municipio\Helper\ContentType::complementPlacePost() method with the complement flag set to false.
     *
     * @return void
     */
    private function setContentTypeViewData()
    {
        if (empty($this->contentType->getKey())) {
            return;
        }

        $contentType = $this->contentType->getKey();

        if ($contentType === 'place') {
            $this->data['post'] = \Municipio\Helper\ContentType::complementPlacePost($this->data['post'], false);
        }
    }
}
