<?php

namespace Municipio\Controller;

use Municipio\Controller\Navigation\MenuBuilderInterface;
use Municipio\Controller\Navigation\MenuDirector;
use WpService\WpService;
use AcfService\AcfService;
use Municipio\Helper\SiteSwitcher\SiteSwitcherInterface;
use Municipio\Helper\User\User;
use Municipio\HooksRegistrar\Hookable;

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

        //Content
        $this->data['heading']    = $this->getHeading();
        $this->data['content']    = $this->getContent();
        $this->data['reviewDate'] = $this->getReviewDate();
        $this->data['compliance'] = (object) [
            'level'  => $this->complianceLevel(),
            'label'  => $this->complianceLevelLabel(),
            'color'  => $this->complianceLevelColor(),
            'reference' => (object) [
                'standard' => $this->acfService->getField('mun_a11ystatement_compliance_reference', 'options') ?: __('WCAG AA', 'municipio'),
                'version'  => $this->acfService->getField('mun_a11ystatement_compliance_reference_version', 'options') ?: __('2.1', 'municipio'),
            ],
        ];

        // Build categories and issues
        $this->data['categorizedIssues'] = $this->getKnownIssues();

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

        $content = $this->wpService->applyFilters('Municipio/A11y/Content', $content);

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
