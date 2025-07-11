<?php

namespace Municipio\Controller;

use Municipio\Controller\Navigation\MenuBuilderInterface;
use Municipio\Controller\Navigation\MenuDirector;
use WpService\WpService;
use AcfService\AcfService;
use Municipio\Helper\SiteSwitcher\SiteSwitcherInterface;
use Municipio\Helper\User\User;

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
        protected AcfService $acfService,
        protected SiteSwitcherInterface $siteSwitcher,
        protected User $userHelper
    ) {
        $this->wpService->statusHeader(403);

        $this->wpService->addFilter('wp_title', array($this, 'setup403Title'));

        parent::__construct(
            $menuBuilder,
            $menuDirector,
            $wpService,
            $acfService,
            $siteSwitcher,
            $userHelper
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

        // Wrapper class
        $wrapperClasses = ['t-403'];

        //Get local instance of wp_query
        $this->globalToLocal('wp_query', 'query');

        //Get current post type to view
        $this->data['postType'] = $this->getRequestedPostType();

        //Heading
        $this->data['heading']    = $this->getHeading();
        $this->data['subheading'] = $this->getSubheading();

        //Actions
        $actionButtons = isset($this->data['customizer']->error403Buttons)
            ? $this->data['customizer']->error403Buttons
            : array_keys(\Municipio\Customizer\Sections\ErrorPages::getButtonChoices('403'));

        $this->data['actionButtons'] = [];
        if (in_array('return', $actionButtons)) {
            $this->data['actionButtons'][] = \Municipio\Controller\Error\Buttons::getReturnButton();
        }
        if (in_array('home', $actionButtons)) {
            $this->data['actionButtons'][] = \Municipio\Controller\Error\Buttons::getHomeButton();
        }

        //Image
        $this->data['image'] = isset($this->data['customizer']->error403Image) && !empty($this->data['customizer']->error403Image)
            ? $this->data['customizer']->error403Image
            : false;

        // Backdrop
        $backdrop = isset($this->data['customizer']->error403Backdrop)
            ? $this->data['customizer']->error403Backdrop
            : true;

        // Wrapper classes
        if ($this->data['image']) {
            $wrapperClasses[] = 't-404--has-image';
        }
        if ($backdrop) {
            $wrapperClasses[] = 't-403--has-error-backdrop';
        }
        $wrapperClasses               = implode(' ', $wrapperClasses);
        $this->data['wrapperClasses'] = $this->wpService->applyFilters('Municipio/403/WrapperClasses', $wrapperClasses);
    }

    /**
     * Returns the heading
     * @return  string
     */
    protected function getHeading(): string
    {
        $heading = isset($this->data['customizer']->error403Heading) && !empty($this->data['customizer']->error403Heading)
            ? $this->data['customizer']->error403Heading
            : \Municipio\Customizer\Sections\ErrorPages::getDefaultHeading('403');
        return $this->wpService->applyFilters('Municipio/403/Heading', $this->wpService->__($heading, 'municipio'), $this->getRequestedPostType());
    }

    /**
     * Returns the body
     * @return string
     */
    protected function getSubheading()
    {
        $subheading = isset($this->data['customizer']->error403Description) && !empty($this->data['customizer']->error403Description)
            ? $this->data['customizer']->error403Description
            : \Municipio\Customizer\Sections\ErrorPages::getDefaultDescription('403');
        return $this->wpService->applyFilters('Municipio/403/Body', 
            $this->wpService->__($subheading, 'municipio'),
            ucfirst($this->getRequestedPostType())
        );
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
