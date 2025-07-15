<?php

namespace Municipio\Controller;

use Municipio\Controller\Navigation\MenuBuilderInterface;
use Municipio\Controller\Navigation\MenuDirector;
use WpService\WpService;
use AcfService\AcfService;
use Municipio\Helper\SiteSwitcher\SiteSwitcherInterface;
use Municipio\Helper\User\User;

/**
 * 401 controller
 */
class A11y extends \Municipio\Controller\BaseController
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
        protected SiteSwitcherInterface $siteSwitcher,
        protected User $userHelper
    ) {
        $this->wpService->statusHeader(401);

        $this->wpService->addFilter('wp_title', array($this, 'setup401Title'));

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
     * Setup 401 title
     *
     * @return string
     */
    public function setup401Title(): string
    {
        return $this->wpService->applyFilters('Municipio/401/Title', 'Accessibility Statement - Municipio');
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        global $wp;

        parent::init();

        // Wrapper class
        $wrapperClasses = ['t-a11y'];

        //Get local instance of wp_query
        $this->globalToLocal('wp_query', 'query');

        //Content
        $this->data['heading']    = $this->getHeading();
        $this->data['content']    = $this->getContent();
       
    }

    /**
     * Returns the heading
     * @return  string
     */
    protected function getHeading(): string
    {
        return $this->wpService->applyFilters(
            'Municipio/401/Heading',
            $this->acfService->getField('heading', 'options-theme-404') ?: __('Accessibility Statement', 'municipio')
        );
    }

    /**
     * Returns the content
     * @return  string
     */
    protected function getContent(): string
    {
        $content = $this->acfService->getField('content', 'options-theme-404');

        if (empty($content)) {
            $content = '<p>' . __('This is the accessibility statement for our website.', 'municipio') . '</p>';
        }

        return $content;
    }
}
