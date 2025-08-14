<?php

namespace Municipio\Controller;

use Municipio\Controller\Navigation\MenuBuilderInterface;
use Municipio\Controller\Navigation\MenuDirector;
use WpService\WpService;
use AcfService\AcfService;
use Municipio\Helper\SiteSwitcher\SiteSwitcherInterface;
use Municipio\Helper\User\User;

/**
 * A11y controller
 */
class A11y extends \Municipio\Controller\Singular
{
    public $query;
    public $view = 'a11y';

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
        $this->addHooks();

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
     * Add hooks for the controller
     */
    public function addHooks()
    {
        $this->wpService->addFilter('wp_title', array($this, 'setupA11yTitle'));
        $this->wpService->addFilter('Municipio/A11y/Content', array($this, 'filterA11yContentTemplateTags'));
    }

    /**
     * Setup A11y title
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

        // Language
        $this->data['lang'] = \Municipio\Helper\TranslatedLabels::getLang(
            array_merge(
                [
                    'complianceLevel'                   => $this->wpService->__('Accessibility Compliance', 'municipio'),
                    'complaint'                         => $this->wpService->__('Compliant', 'municipio'),
                    'partiallyComplaint'                => $this->wpService->__('Partially compliant', 'municipio'),
                    'notCompliant'                      => $this->wpService->__('Not compliant', 'municipio'),
                    'noAccessibilityStatementAvailable' => $this->wpService->__('No accessibility statement available.', 'municipio'),
                    'reviewDate'                        => $this->wpService->__('Last reviewed', 'municipio'),
                ],
                (array) $this->data['lang'] ?? []
            )
        );

        //Content
        $this->data['heading']    = $this->getHeading();
        $this->data['content']    = $this->getContent();

        // Review
        $this->data['review'] = (object) [
            'date'   => $this->getReviewDate(),
            'status' => $this->getReviewStatus(),
            'icon'   => $this->getReviewStatusIcon(),
            'label'  => $this->getReviewStatusLabel(),
            'class'  => $this->getReviewStatusClassList(),
        ];
        
        // Compliance
        $this->data['compliance'] = (object) [
            'level'  => $this->getComplianceLevel(),
            'label'  => $this->getComplianceLevelLabel(),
            'icon'   => $this->getComplianceLevelIcon(),
            'class'  => $this->getComplianceLevelClassList(),
            'reference' => (object) [
                'standard' => $this->acfService->getField('mun_a11ystatement_compliance_reference', 'options') ?: 'WCAG AA',
                'version'  => $this->acfService->getField('mun_a11ystatement_compliance_reference_version', 'options') ?: '2.1',
            ],
        ];

        // Build categories and issues
        $this->data['categorizedIssues'] = $this->getKnownIssues();

    }

    /**
     * Get the review date from ACF options.
     * Returns the date formatted according to the site's date format setting.
     * Returns null if no review date is set.
     */
    private function getReviewDate($readable = true): ?string
    {
        $reviewDate = $this->acfService->getField('mun_a11ystatement_review_date', 'options');

        if (empty($reviewDate)) {
            return null;
        }

        if (!$readable) {
            return strtotime($reviewDate);
        }

        return date_i18n(get_option('date_format'), strtotime($reviewDate));
    }

    /* 
    * Get the review status, a review should be done at least once a year 
    * Returns one of the following:
    * - ok:             recently reviewed
    * - near_deadline:  review should be done within 3 months
    * - overdue:        review is overdue
    */
    private function getReviewStatus(): string
    {
        $reviewTimestamp      = $this->getReviewDate(false);
        $currentTimestamp     = strtotime('today midnight');

        $oneYearInSeconds     = YEAR_IN_SECONDS;
        $threeMonthsInSeconds = MONTH_IN_SECONDS * 3;

        $elapsedTime = $currentTimestamp - $reviewTimestamp;

        if ($elapsedTime >= $oneYearInSeconds) {
            return 'overdue';
        }

        if ($elapsedTime >= ($oneYearInSeconds - $threeMonthsInSeconds)) {
            return 'near_deadline';
        }

        return 'ok';
    }


    /**
     * Get the icon for the review status.
     * Returns an icon class based on the review status.
     * 
     * @return string
     */
    private function getReviewStatusIcon(): string
    {
        $status = $this->getReviewStatus();

        switch ($status) {
            case 'ok':
                return 'check_circle';
            case 'near_deadline':
                return 'warning';
            case 'overdue':
                return 'error';
            default:
                return 'help';
        }
    }

    /**
     * Get the review status label.
     * Returns a translated label based on the review status.
     * 
     * @return string
     */
    private function getReviewStatusLabel(): string
    {
        $status = $this->getReviewStatus();

        switch ($status) {
            case 'ok':
                return __('Recently reviewed', 'municipio');
            case 'near_deadline':
                return __('Review due soon', 'municipio');
            case 'overdue':
                return __('Review overdue', 'municipio');
            default:
                return __('Unknown review status', 'municipio');
        }
    }

    /**
     * Get the class list for the review status.
     * Returns an array of classes based on the review status.
     * 
     * @return array
     */
    private function getReviewStatusClassList(): array
    {
        $status = $this->getReviewStatus();

        switch ($status) {
            case 'ok':
                $reviewStatusClassList = ['u-color__bg--success', 'u-color__text--darkest'];
                break;
            case 'near_deadline':
                $reviewStatusClassList = ['u-color__bg--warning', 'u-color__text--darkest'];
                break;
            case 'overdue':
                $reviewStatusClassList = ['u-color__bg--danger', 'u-color__text--darkest'];
                break;
            default:
                $reviewStatusClassList = ['u-color__bg--dark', 'u-color__text--lightest'];
        }

        return array_merge(
            ['t-a11y-pill'],
            $reviewStatusClassList,
            [$status]
        );
    }

    /**
     * Returns the heading
     * @return  string
     */
    protected function getHeading(): string
    {
        return $this->wpService->applyFilters(
            'Municipio/401/Heading',
            $this->acfService->getField('mun_a11ystatement_title', 'options') ?: $this->wpService->__('Accessibility Statement', 'municipio')
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
            $content = '<p>' . $this->data['lang']->noAccessibilityStatementAvailable . '.' . '</p>';
        }

        $content = $this->wpService->applyFilters('Municipio/A11y/Content', $content);

        return $content;
    }

    

    /**
     * Returns the compliance level
     * @return  string
     */
    protected function getComplianceLevel(): string
    {
        return $this->acfService->getField('mun_a11ystatement_compliance_level', 'options') ?: 'unknown';
    }

    /**
     * Returns the compliance level icon
     * @return  string
     */
    protected function getComplianceLevelIcon(): string
    {
        $complianceLevel = $this->getComplianceLevel();

        switch ($complianceLevel) {
            case 'compliant':
                return 'accessible';
            case 'partially_compliant':
                return 'wheelchair_pickup';
            case 'not_compliant':
                return 'not_accessible';
            default:
                return 'help';
        }
    }

    /**
     * Returns the compliance level label
     * @return  string
     */
    protected function getComplianceLevelLabel(): string
    {
        $complianceLevel = $this->getComplianceLevel();

        switch ($complianceLevel) {
            case 'compliant':
                return $this->data['lang']->complaint ?? '';
            case 'partially_compliant':
                return $this->data['lang']->partiallyComplaint ?? '';
            case 'not_compliant':
                return $this->data['lang']->notCompliant ?? '';
            default:
                return $this->data['lang']->unknown ?? '';
        }
    }

    /**
     * Returns the compliance level classlist
     * @return  array
     */
    protected function getComplianceLevelClassList(): array
    {
        $complianceLevel = $this->getComplianceLevel();

        switch ($complianceLevel) {
            case 'compliant':
                $complianceLevelClassList = ['u-color__bg--success'];
                break;
            case 'partially_compliant':
                $complianceLevelClassList = ['u-color__bg--warning'];
                break;
            case 'not_compliant':
                $complianceLevelClassList = ['u-color__bg--danger'];
                break;
            default:
                $complianceLevelClassList = ['u-color__bg--dark'];
        }

        return array_merge(
            ['u-color__text--darkest', 't-a11y-pill'],
            $complianceLevelClassList,
            [$complianceLevel]
        );
    }

    /**
     * Returns the template tags
     * @return array
     */
    protected function getTemplateTags(): array
    {
        return [
            'website_name' => $this->wpService->getBlogInfo('name'),
            'website_domain' => function () {
                $url    = $this->wpService->getBlogInfo('url');
                $host   = parse_url($url, PHP_URL_HOST);
                return $host ?: $url;
            }
        ];
    }

    /**
     * Filter the content to replace template tags with their values.
     * 
     * Example usage: {{website_name}} will be replaced with the site name.
     *
     * @param string $content
     * @return string
     */
    public function filterA11yContentTemplateTags(string $content): string
    {
        $templateTags = $this->getTemplateTags();
        foreach ($templateTags as $key => $value) {
            if (is_callable($value)) {
                $value = $value();
            }
            $content = str_replace("{{{$key}}}", $value, $content);
        }
        return $content;
    }

    /**
     * Get known issues from the ACF options, normalized by category.
     * 
     * @return array|null
     */
    private function getKnownIssues(?string $filterCategory = null): ?array
    {
        $rawIssues = $this->acfService->getField('mun_a11ystatement_known_issues', 'options');

        if (empty($rawIssues) || !is_array($rawIssues)) {
            return null;
        }

        $grouped = [];

        foreach ($rawIssues as $issue) {
            $category = $issue['category'] ?? null;

            if (is_array($category) && isset($category['value'], $category['label'])) {
                $key = $category['value'];
                $label = $category['label'];
            } elseif (is_string($category)) {
                $key = $label = $category;
            } else {
                $key = $label = 'uncategorized';
            }

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'key' => $key,
                    'label' => $label,
                    'icon' => $this->getIconForCategory($key),
                    'issues' => [],
                ];
            }

            $grouped[$key]['issues'][] = [
                'label' => $issue['label'] ?? '',
            ];
        }

        if ($filterCategory !== null) {
            return isset($grouped[$filterCategory]) ? [$grouped[$filterCategory]] : [];
        }

        return array_values($grouped);
    }

    /**
     * Get the icon for a given category key.
     * 
     * @param string $categoryKey
     * @return string
     */
    private function getIconForCategory(string $categoryKey): string
    {
        // Map category keys to icons
        $icons = [
            'vision' => 'visibility_off',
            'color' => 'format_paint',
            'mobility' => 'pan_tool_alt',
            'hearing' => 'hearing',
            'cognitive' => 'psychology',
        ];

        return $icons[$categoryKey] ?? 'accessibility_new';
    }
}
