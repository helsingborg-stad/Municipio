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
        $this->wpService->statusHeader(200);

        $this->wpService->addFilter('wp_title', array($this, 'setupA11yTitle'));

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
    public function setupA11yTitle(): string
    {
        return $this->wpService->applyFilters('Municipio/A11y/Title', $this->getHeading());
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
        $this->data['reviewDate'] = $this->getReviewDate();
        $this->data['compliance'] = (object) [
            'level'  => $this->complianceLevel(),
            'label'  => $this->complianceLevelLabel(),
            'color'  => $this->complianceLevelColor(),
        ];
    }

    /**
     * Returns the heading
     * @return  string
     */
    protected function getHeading(): string
    {
        return $this->wpService->applyFilters(
            'Municipio/401/Heading',
            $this->acfService->getField('mun_a11ystatement_title', 'options') ?: __('Accessibility Statement', 'municipio')
        );
    }

    /**
     * Returns the content
     * @return  string
     */
    protected function getContent(): string
    {
        $content = $this->acfService->getField('mun_a11ystatement_preamble', 'options');

        if (empty($content)) {
            $content = '<p>' . __('No accessability statement avabile.', 'municipio') . '</p>';
        }

        return $content;
    }

    /**
     * Returns the review date
     * @return  string
     */
    protected function getReviewDate(): string
    {
        $reviewDate = $this->acfService->getField('mun_a11ystatement_review_date', 'options');

        if (empty($reviewDate)) {
            $reviewDate = __('No review date available.', 'municipio');
        } else {
            $reviewDate = date_i18n(get_option('date_format'), strtotime($reviewDate));
        }

        return $reviewDate;
    }

    /**
     * Returns the compliance level
     * @return  string
     */
    protected function complianceLevel(): string
    {
        return $this->acfService->getField('mun_a11ystatement_compliance_level', 'options');
    }

    /**
     * Returns the compliance level label
     * @return  string
     */
    protected function complianceLevelLabel(): string
    {
        $complianceLevel = $this->complianceLevel();

        switch ($complianceLevel) {
            case 'compliant':
                return __('Compliant', 'municipio');
            case 'partially_compliant':
                return __('Partially compliant', 'municipio');
            case 'non_compliant':
                return __('Non compliant', 'municipio');
            default:
                return __('Unknown', 'municipio');
        }
    }

    /**
     * Returns the compliance level color
     * @return  string
     */
    protected function complianceLevelColor(): string
    {
        $complianceLevel = $this->complianceLevel();

        switch ($complianceLevel) {
            case 'compliant':
                return 'green';
            case 'partially_compliant':
                return 'yellow';
            case 'non_compliant':
                return 'red';
            default:
                return 'grey';
        }
    }
}
