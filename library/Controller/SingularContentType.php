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
        $postType = $this->data['post']->postType;
        $this->contentType = \Municipio\Helper\ContentType::getContentType($postType);

        $this->setContentTypeViewData();
        $this->addContentTypeHooks();

        // Checks if the content type template should be skipped and sets the view accordingly.
        if (!\Municipio\Helper\ContentType::skipContentTypeTemplate($postType)) {
            $this->view = $this->contentType->getView();
        }

        $this->data['structuredData'] = $this->appendStructuredData();
    }

    /**
     * Initializes the controller.
     */
    public function init(): void
    {
        parent::init();
    }

    /**
     * Appends structured data to the view data.
     *
     * @return string|null The structured data as a JSON string, or null if not applicable.
     */
    public function appendStructuredData(): ?string
    {
        return \Municipio\Helper\Data::normalizeStructuredData(
            $this->contentType->getStructuredData($this->postId)
        );
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
        if('place' === $contentType) {
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

        $secondaryContentTypes = $this->contentType->getSecondaryContentType() ?? [];
        foreach ($secondaryContentTypes as $secondary) {
            if ($secondary) {
                $secondary->addHooks();
            }
        }
    }
}
