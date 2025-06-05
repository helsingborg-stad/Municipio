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

        // Wrapper class
        $wrapperClasses = ['t-401'];

        //Get local instance of wp_query
        $this->globalToLocal('wp_query', 'query');

        //Get current post type to view
        $this->data['postType'] = $this->getRequestedPostType();

        //Content
        $this->data['heading']    = $this->getHeading();
        $this->data['subheading'] = $this->getSubheading();

        // Current URL
        $currentUrl = $this->wpService->escUrl($this->wpService->homeUrl($this->wpService->addQueryArg(array($_GET), $wp->request)));

        //Actions
        $actionButtons = isset($this->data['customizer']->error401Buttons)
            ? $this->data['customizer']->error401Buttons
            : array_keys(\Municipio\Customizer\Sections\ErrorPages::getButtonChoices('401'));

        $this->data['actionButtons'] = [];
        if (in_array('return', $actionButtons)) {
            $this->data['actionButtons'][] = \Municipio\Controller\Error\Buttons::getReturnButton();
        }
        if (in_array('home', $actionButtons)) {
            $this->data['actionButtons'][] = \Municipio\Controller\Error\Buttons::getHomeButton();
        }
        if (in_array('login', $actionButtons)) {
            $this->data['actionButtons'][] = \Municipio\Controller\Error\Buttons::getLoginButton([], $currentUrl);
        }

        //Image
        $this->data['image'] = isset($this->data['customizer']->error401Image) && !empty($this->data['customizer']->error401Image)
            ? $this->data['customizer']->error401Image
            : false;

        // Backdrop
        $backdrop = isset($this->data['customizer']->error401Backdrop) 
            ? $this->data['customizer']->error401Backdrop
            : true;

        // Extra wrapper classes
        if ($this->data['image']) {
            $wrapperClasses[] = 't-404--has-image';
        }
        if ($backdrop) {
            $wrapperClasses[] = 't-401--has-error-backdrop';
        }
        $wrapperClasses = implode(' ', $wrapperClasses);
        $this->data['wrapperClasses'] = $this->wpService->applyFilters('Municipio/401/WrapperClasses', $wrapperClasses);
    }

    /**
     * Returns the heading
     * @return  string
     */
    protected function getHeading(): string
    {
        $heading = isset($this->data['customizer']->error401Heading) && !empty($this->data['customizer']->error401Heading)
            ? $this->data['customizer']->error401Heading
            : \Municipio\Customizer\Sections\ErrorPages::getDefaultHeading('401');
        return $this->wpService->applyFilters('Municipio/401/Heading', $this->wpService->__($heading, 'municipio'), $this->getRequestedPostType());
    }

    /**
     * Returns the body
     * @return string
     */
    protected function getSubheading()
    {
        $subheading = isset($this->data['customizer']->error401Description) && !empty($this->data['customizer']->error401Description)
            ? $this->data['customizer']->error401Description
            : \Municipio\Customizer\Sections\ErrorPages::getDefaultDescription('401');
        return $this->wpService->applyFilters('Municipio/401/Body', $this->wpService->__($subheading, 'municipio'), $this->getRequestedPostType());
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
