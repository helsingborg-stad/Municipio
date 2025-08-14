<?php

namespace Municipio\Controller;

use Municipio\Controller\Navigation\MenuBuilderInterface;
use Municipio\Controller\Navigation\MenuDirector;
use WpService\WpService;
use AcfService\AcfService;
use Municipio\Helper\SiteSwitcher\SiteSwitcherInterface;
use Municipio\Helper\User\User;

enum ReviewStatus {
    case OK;
    case NearDeadline;
    case Overdue;
    case Unknown;

    public function getIcon(): string
    {
        return match ($this) {
            self::OK => 'check_circle',
            self::NearDeadline => 'warning',
            self::Overdue => 'error',
            default => 'help',
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::OK => __('Recently reviewed', 'municipio'),
            self::NearDeadline => __('Review due soon', 'municipio'),
            self::Overdue => __('Review overdue', 'municipio'),
            default => __('Unknown review status', 'municipio'),
        };
    }
}

enum ComplianceLevel {
    case Compliant;
    case PartiallyCompliant;
    case NotCompliant;
    case Unknown;

    public function getIcon(): string
    {
        return match ($this) {
            self::Compliant => 'accessible',
            self::PartiallyCompliant => 'wheelchair_pickup',
            self::NotCompliant => 'not_accessible',
            default => 'help',
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::Compliant => __('Compliant', 'municipio'),
            self::PartiallyCompliant => __('Partially compliant', 'municipio'),
            self::NotCompliant => __('Not compliant', 'municipio'),
            default => __('Unknown compliance level', 'municipio'),
        };
    }
}

/**
 * 401 controller
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

        // Language
        $this->data['lang'] = \Municipio\Helper\TranslatedLabels::getLang(
            array_merge(
                [
                    'complianceLevel'                   => __('Accessibility Compliance', 'municipio'),
                    'complaint'                         => __('Compliant', 'municipio'),
                    'partiallyComplaint'                => __('Partially compliant', 'municipio'),
                    'notCompliant'                      => __('Not compliant', 'municipio'),
                    'noAccessibilityStatementAvailable' => __('No accessibility statement available.', 'municipio'),
                    'reviewDate'                        => __('Last reviewed', 'municipio'),
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
    private function getReviewDate(bool $readable = true): ?string
    {
        $reviewDate = $this->acfService->getField('mun_a11ystatement_review_date', 'options');

        if (empty($reviewDate)) {
            return null;
        }

        if (!$readable) {
            return strtotime($reviewDate);
        }

        return date_i18n($this->wpService->getOption('date_format'), strtotime($reviewDate));
    }

    /* 
    * Get the review status, a review should be done at least once a year 
    * Returns one of the following:
    * - ok:             recently reviewed
    * - near_deadline:  review should be done within 3 months
    * - overdue:        review is overdue
    */
    private function getReviewStatus(): ReviewStatus
    {
        $reviewTimestamp      = $this->getReviewDate(false);
        $currentTimestamp     = strtotime('today midnight');

        $oneYearInSeconds     = YEAR_IN_SECONDS;
        $threeMonthsInSeconds = MONTH_IN_SECONDS * 3;

        if ($reviewTimestamp === null) {
            return ReviewStatus::Unknown;
        }

        $elapsedTime = $currentTimestamp - $reviewTimestamp;

        if ($elapsedTime >= $oneYearInSeconds) {
            return ReviewStatus::Overdue;
        }

        if ($elapsedTime >= ($oneYearInSeconds - $threeMonthsInSeconds)) {
            return ReviewStatus::NearDeadline;
        }

        return ReviewStatus::OK;
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
        return $status->getIcon();
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
        return $status->getLabel();
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

        $reviewStatusClassList = match ($status) {
            ReviewStatus::OK => ['u-color__bg--success', 'u-color__text--darkest'],
            ReviewStatus::NearDeadline => ['u-color__bg--warning', 'u-color__text--darkest'],
            ReviewStatus::Overdue => ['u-color__bg--danger', 'u-color__text--darkest'],
            default => ['u-color__bg--dark', 'u-color__text--lightest'],
        };

        return array_merge(
            ['t-a11y-pill'],
            $reviewStatusClassList,
            [$status->name]
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
            $content = '<p>' . $this->data['lang']->noAccessibilityStatementAvailable . '.' . '</p>';
        }

        $content = $this->wpService->applyFilters('Municipio/A11y/Content', $content);

        return $content;
    }

    

    /**
     * Returns the compliance level
     * @return  ComplianceLevel
     */
    protected function getComplianceLevel(): ComplianceLevel
    {
        $value = $this->acfService->getField('mun_a11ystatement_compliance_level', 'options') ?: 'unknown';

        return match ($value) {
            'compliant' => ComplianceLevel::Compliant,
            'partially_compliant' => ComplianceLevel::PartiallyCompliant,
            'not_compliant' => ComplianceLevel::NotCompliant,
            default => ComplianceLevel::Unknown,
        };
    }

    /**
     * Returns the compliance level icon
     * @return  string
     */
    protected function getComplianceLevelIcon(): string
    {
        $complianceLevel = $this->getComplianceLevel();
        return $complianceLevel->getIcon();
    }

    /**
     * Returns the compliance level label
     * @return  string
     */
    protected function getComplianceLevelLabel(): string
    {
        $complianceLevel = $this->getComplianceLevel();

        return match ($complianceLevel) {
            ComplianceLevel::Compliant => $this->data['lang']->complaint ?? '',
            ComplianceLevel::PartiallyCompliant => $this->data['lang']->partiallyComplaint ?? '',
            ComplianceLevel::NotCompliant => $this->data['lang']->notCompliant ?? '',
            default => $this->data['lang']->unknown ?? '',
        };
    }

    /**
     * Returns the compliance level classlist
     * @return  array
     */
    protected function getComplianceLevelClassList(): array
    {
        $complianceLevel = $this->getComplianceLevel();

        $complianceLevelClassList = match ($complianceLevel) {
            ComplianceLevel::Compliant => ['u-color__bg--success'],
            ComplianceLevel::PartiallyCompliant => ['u-color__bg--warning'],
            ComplianceLevel::NotCompliant => ['u-color__bg--danger'],
            default => ['u-color__bg--dark'],
        };

        return array_merge(
            ['u-color__text--darkest', 't-a11y-pill'],
            $complianceLevelClassList,
            [$complianceLevel->name]
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
