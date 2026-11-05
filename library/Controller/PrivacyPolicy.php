<?php

namespace Municipio\Controller;

use AcfService\AcfService;
use Municipio\Controller\Navigation\MenuBuilderInterface;
use Municipio\Controller\Navigation\MenuDirector;
use Municipio\Helper\SiteSwitcher\SiteSwitcherInterface;
use Municipio\Helper\User\User;
use WpService\WpService;

/**
 * Privacy policy controller
 */
class PrivacyPolicy extends \Municipio\Controller\Singular
{
    public $query;
    public $view = 'privacy-policy';

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
        $this->wpService->addFilter('wp_title', [$this, 'setupPrivacyPolicyTitle']);
        $this->wpService->addFilter('the_seo_framework_query_supports_seo', [$this, 'supportsSeo']);
        $this->wpService->addFilter('the_seo_framework_title_from_generation', [$this, 'getSeoTitle'], 10, 2);
        $this->wpService->addFilter('the_seo_framework_custom_field_description', [$this, 'getSeoDescription'], 10, 2);
        $this->wpService->addFilter('the_seo_framework_generated_description', [$this, 'getSeoDescription'], 10, 3);
        $this->wpService->addFilter('Municipio/PrivacyPolicy/Content', [$this, 'filterContentTemplateTags']);
    }

    public function setupPrivacyPolicyTitle(): string
    {
        return $this->wpService->applyFilters('Municipio/PrivacyPolicy/Title', $this->getHeading());
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->data['lang'] = \Municipio\Helper\TranslatedLabels::getLang(
            array_merge(
                [
                    'noPrivacyPolicyAvailable' => $this->wpService->__('No privacy policy available.', 'municipio'),
                    'lastUpdated'              => $this->wpService->__('Last updated', 'municipio'),
                ],
                (array) $this->data['lang'] ?? []
            )
        );

        $this->data['heading']     = $this->getHeading();
        $this->data['content']     = $this->getContent();
        $this->data['lastUpdated'] = $this->getLastUpdated();

        $this->ensureBreadcrumbMenu();
        $this->ensurePrintMenuItem();
    }

    protected function getHeading(): string
    {
        return $this->wpService->applyFilters(
            'Municipio/PrivacyPolicy/Heading',
            $this->acfService->getField('mun_privacypolicy_title', 'options') ?: $this->wpService->__('Privacy Policy', 'municipio')
        );
    }

    /**
     * Makes the virtual privacy policy available to The SEO Framework.
     */
    public function supportsSeo(bool $supported): bool
    {
        return true;
    }

    public function getSeoTitle(string $title, ?array $args = null): string
    {
        return $this->getHeading();
    }

    public function getSeoDescription(string $description, ?array $args = null, string $type = 'search'): string
    {
        $description = $this->acfService->getField('mun_privacypolicy_seo_description', 'options');

        if (!is_string($description) || trim($description) === '') {
            $description = $this->wpService->__('This privacy policy describes how we process your personal data.', 'municipio');
        }

        return $this->wpService->applyFilters('Municipio/PrivacyPolicy/MetaDescription', $description);
    }

    protected function getContent(): string
    {
        $content = $this->acfService->getField('mun_privacypolicy_content', 'options');

        if (empty($content)) {
            $content = '<p>' . $this->data['lang']->noPrivacyPolicyAvailable . '</p>';
        }

        return $this->wpService->applyFilters('Municipio/PrivacyPolicy/Content', $content);
    }

    /**
     * Get the last updated date formatted according to the site's date format.
     */
    private function getLastUpdated(): ?string
    {
        $date = $this->acfService->getField('mun_privacypolicy_last_updated', 'options');

        if (empty($date) || !strtotime($date)) {
            return null;
        }

        return (string) date_i18n($this->wpService->getOption('date_format'), strtotime($date));
    }

    /**
     * The virtual page is treated as a 404 by WordPress while its custom template
     * is resolved. Replace the missing breadcrumb menu with standard data.
     */
    private function ensureBreadcrumbMenu(): void
    {
        if (!empty($this->data['breadcrumbMenu']['items'])) {
            return;
        }

        $this->data['breadcrumbMenu'] = [
            'name'       => 'breadcrumb',
            'identifier' => 'breadcrumb',
            'items'      => [
                'home' => [
                    'label'   => __('Home', 'municipio'),
                    'href'    => $this->data['homeUrl'],
                    'current' => false,
                    'icon'    => 'home',
                ],
                'privacy-policy' => [
                    'label'   => wp_strip_all_tags($this->data['heading']),
                    'href'    => '',
                    'current' => true,
                ],
            ],
        ];
    }

    /**
     * Add the regular print action, which BaseController cannot add when the
     * virtual route is evaluated as a 404 instead of a WordPress page.
     */
    private function ensurePrintMenuItem(): void
    {
        if (!empty($this->data['accessibilityMenu']['items']['print'])) {
            return;
        }

        $this->data['accessibilityMenu']['items']['print'] = [
            'icon'   => 'print',
            'href'   => '#',
            'script' => 'window.print();return false;',
            'text'   => __('Print', 'municipio'),
            'label'  => __('Print this page', 'municipio'),
        ];
    }

    protected function getTemplateTags(): array
    {
        return [
            'website_name'   => $this->wpService->getBlogInfo('name'),
            'website_domain' => function () {
                $url  = $this->wpService->getBlogInfo('url');
                $host = parse_url($url, PHP_URL_HOST);
                return $host ?: $url;
            },
        ];
    }

    /**
     * Replace template tags, e.g. {{website_name}}, with their values.
     */
    public function filterContentTemplateTags(string $content): string
    {
        foreach ($this->getTemplateTags() as $key => $value) {
            if (is_callable($value)) {
                $value = $value();
            }
            $content = str_replace("{{{$key}}}", (string) $value, $content);
        }

        return $content;
    }
}
