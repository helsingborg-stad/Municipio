<?php

namespace Municipio\Controller;

use AcfService\AcfService;
use Municipio\Controller\Navigation\MenuBuilderInterface;
use Municipio\Controller\Navigation\MenuDirector;
use Municipio\Controller\VirtualPage\VirtualPageControllerTrait;
use Municipio\Helper\SiteSwitcher\SiteSwitcherInterface;
use Municipio\Helper\User\User;
use WpService\WpService;

/**
 * Privacy policy controller
 */
class PrivacyPolicy extends \Municipio\Controller\Singular
{
    use VirtualPageControllerTrait;

    public $query;
    public $view = 'privacy-policy';

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
        $this->wpService->addFilter('wp_title', [$this, 'setupPrivacyPolicyTitle']);
        $this->addSeoHooks();
        $this->wpService->addFilter('Municipio/PrivacyPolicy/Content', [$this, 'filterContentTemplateTags']);
    }

    /**
     * Setup privacy policy title
     */
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

        $this->ensureBreadcrumbMenu('privacy-policy');
        $this->ensurePrintMenuItem();
    }

    /**
     * Returns the heading
     */
    protected function getHeading(): string
    {
        $heading = $this->wpService->applyFilters(
            'Municipio/PrivacyPolicy/Heading',
            $this->acfService->getField('mun_privacypolicy_title', 'options') ?: $this->wpService->__('Privacy Policy', 'municipio')
        );

        return wp_strip_all_tags((string) $heading);
    }

    /**
     * Returns the configured SEO description, with a translated default.
     */
    protected function getMetaDescription(): string
    {
        $description = $this->acfService->getField('mun_privacypolicy_seo_description', 'options');

        if (!is_string($description) || trim($description) === '') {
            $description = $this->wpService->__('This privacy policy describes how we process your personal data.', 'municipio');
        }

        return $this->wpService->applyFilters('Municipio/PrivacyPolicy/MetaDescription', $description);
    }

    /**
     * Returns the content
     */
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
        $date      = $this->acfService->getField('mun_privacypolicy_last_updated', 'options');
        $timestamp = is_string($date) ? strtotime($date) : false;

        if ($timestamp === false) {
            return null;
        }

        return (string) date_i18n($this->wpService->getOption('date_format'), $timestamp);
    }

    /**
     * Replace template tags, e.g. {{website_name}}, in the content.
     */
    public function filterContentTemplateTags(string $content): string
    {
        return $this->replaceTemplateTags($content);
    }
}
