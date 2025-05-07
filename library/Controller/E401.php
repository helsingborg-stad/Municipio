<?php

namespace Municipio\Controller;

use Municipio\Controller\Navigation\MenuBuilderInterface;
use Municipio\Controller\Navigation\MenuDirector;
use WpService\WpService;
use AcfService\AcfService;
use Municipio\Helper\SiteSwitcher\SiteSwitcher;

/**
 * 401 controller
 */
class E401 extends \Municipio\Controller\BaseController
{
    public $query;

    /**
     * Constructor
     */
    public function __construct(
        protected MenuBuilderInterface $menuBuilder,
        protected MenuDirector $menuDirector,
        protected WpService $wpService,
        protected AcfService $acfService,
        protected SiteSwitcher $siteSwitcher
    ) {
        $this->wpService->statusHeader(401);

        $this->wpService->addFilter('wp_title', array($this, 'setup401Title'));

        parent::__construct(
            $menuBuilder,
            $menuDirector,
            $wpService,
            $acfService,
            $siteSwitcher
        );
    }

    /**
     * Setup 401 title
     *
     * @return string
     */
    public function setup401Title(): string
    {
        return $this->wpService->applyFilters('Municipio/401/Title', '401 - Municipio');
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        global $wp;

        parent::init();

        //Get local instance of wp_query
        $this->globalToLocal('wp_query', 'query');

        //Get current post type to view
        $this->data['postType'] = $this->getRequestedPostType();

        //Heading
        $this->data['heading'] = $this->getHeading();

        // Current URL
        $currentUrl = $this->wpService->escUrl($this->wpService->homeUrl($this->wpService->addQueryArg(array($_GET), $wp->request)));

        //Actions
        $this->data['actionButtons']   = [];
        $this->data['actionButtons'][] = \Municipio\Controller\Error\Buttons::getReturnButton();
        $this->data['actionButtons'][] = \Municipio\Controller\Error\Buttons::getHomeButton();
        $this->data['actionButtons'][] = \Municipio\Controller\Error\Buttons::getLoginButton([], $currentUrl);
    }

    /**
     * Returns the heading
     * @return  string
     */
    protected function getHeading(): string
    {
        return $this->wpService->applyFilters('Municipio/401/Heading', $this->wpService->__("This post is password protected, please log in to view this post.", 'municipio'), $this->getRequestedPostType());
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

        return $this->wpService->applyFilters('Municipio/401/PostType', $postType);
    }
}
