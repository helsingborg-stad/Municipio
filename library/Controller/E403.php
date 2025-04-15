<?php

namespace Municipio\Controller;

use Municipio\Controller\Navigation\MenuBuilderInterface;
use Municipio\Controller\Navigation\MenuDirector;
use WpService\WpService;
use AcfService\AcfService;

/**
 * 403 Controller
 */
class E403 extends \Municipio\Controller\BaseController
{
    public $query;

    /**
     * Constructor
     * @param MenuBuilderInterface $menuBuilder
     * @param MenuDirector $menuDirector
     * @param WpService $wpService
     * @param AcfService $acfService
     */
    public function __construct(
        protected MenuBuilderInterface $menuBuilder,
        protected MenuDirector $menuDirector,
        protected WpService $wpService,
        protected AcfService $acfService
    ) {
        $this->wpService->statusHeader(403);

        $this->wpService->addFilter('wp_title', array($this, 'setup403Title'));

        parent::__construct(
            $menuBuilder,
            $menuDirector,
            $wpService,
            $acfService,
            $siteSwitcher
        );
    }

    /**
     * Set the title
     * @return string
     */
    public function setup403Title(): string
    {
        return $this->wpService->applyFilters('Municipio/403/Title', '403 - Municipio');
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        //Get local instance of wp_query
        $this->globalToLocal('wp_query', 'query');

        //Get current post type to view
        $this->data['postType'] = $this->getRequestedPostType();

        //Heading
        $this->data['heading'] = $this->getHeading();

        //Actions
        $this->data['actionButtons']   = [];
        $this->data['actionButtons'][] = \Municipio\Controller\Error\Buttons::getReturnButton();
        $this->data['actionButtons'][] = \Municipio\Controller\Error\Buttons::getHomeButton();
    }

    /**
     * Returns the heading
     * @return  string
     */
    protected function getHeading(): string
    {
        return $this->wpService->applyFilters('Municipio/403/Heading', $this->wpService->__("Your user group do not have access to view this post.", 'municipio'), $this->getRequestedPostType());
    }

    /**
     * Returns the posttype requested, if post found, default to post.
     * @return string
     */
    private function getRequestedPostType(): string
    {
        //Get queried posttype
        if (isset($this->query->query) && isset($this->query->query['post_type'])) {
            $postType = $this->query->query['post_type'];
        }

        //Default to page if not set
        if (!isset($postType) || is_null($postType)) {
            $postType = $this->wpService->__("post");
        }

        return $this->wpService->applyFilters('Municipio/403/PostType', $postType);
    }
}
