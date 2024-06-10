<?php

namespace Municipio\Controller;

/**
 * Handles singular content types, setting up necessary view data and hooks.
 */
class SingularContentType extends \Municipio\Controller\Singular
{
    public $view;
    protected $postId;
    protected $contentType;

    /**
     * Constructs the SingularContentType object, sets up content type information and view data.
     */
    public function __construct()
    {
        parent::__construct();

        $this->postId = $this->data['post']->id;

        // Retrieves the content type of the current post.
        $postType          = $this->data['post']->postType;
        $this->contentType = \Municipio\Helper\ContentType::getContentType($postType);

        $this->setContentTypeViewData();
        $this->addContentTypeHooks();

        $this->view = $this->contentType->getView();
    }

    /**
     * Initializes the controller.
     */
    public function init(): void
    {
        parent::init();
    }

    /**
     * Sets up view data based on the content type of the current post.
     *
     * @return void
     */
    private function setContentTypeViewData(): void
    {
        $contentType = $this->contentType->getKey();

        if (empty($contentType)) {
            return;
        }

        // Handles specific content types
        if ('Place' === $contentType) {
            $this->data['post'] = \Municipio\Helper\ContentType::complementPlacePost($this->data['post']);
        }
    }

    /**
     * Adds hooks for the primary and secondary content types.
     *
     * @return void
     */
    protected function addContentTypeHooks(): void
    {
        if ($this->contentType) {
            $this->contentType->addHooks();
        }
    }
}
