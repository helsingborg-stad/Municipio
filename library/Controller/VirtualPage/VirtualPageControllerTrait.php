<?php

namespace Municipio\Controller\VirtualPage;

/**
 * Shared behaviour for controllers that render virtual pages, i.e. pages that are
 * served through a rewrite rule instead of a WordPress post (for example the
 * accessibility statement and the privacy policy).
 *
 * Such requests are evaluated as 404 by WordPress while the custom template is
 * resolved, so breadcrumbs, the print action and SEO data must be supplied here.
 */
trait VirtualPageControllerTrait
{
    /**
     * The heading displayed on the page, also used as SEO title.
     */
    abstract protected function getHeading(): string;

    /**
     * The meta description used by The SEO Framework.
     */
    abstract protected function getMetaDescription(): string;

    /**
     * Register The SEO Framework hooks for the virtual page.
     */
    protected function addSeoHooks(): void
    {
        $this->wpService->addFilter('the_seo_framework_query_supports_seo', [$this, 'supportsSeo']);
        $this->wpService->addFilter('the_seo_framework_title_from_generation', [$this, 'getSeoTitle']);
        $this->wpService->addFilter('the_seo_framework_custom_field_description', [$this, 'getSeoDescription']);
        $this->wpService->addFilter('the_seo_framework_generated_description', [$this, 'getSeoDescription']);
    }

    /**
     * Makes the virtual page available to The SEO Framework.
     */
    public function supportsSeo(): bool
    {
        return true;
    }

    /**
     * Returns the same title that is displayed on the page.
     */
    public function getSeoTitle(): string
    {
        return $this->getHeading();
    }

    /**
     * Returns the meta description for the page.
     */
    public function getSeoDescription(): string
    {
        return $this->getMetaDescription();
    }

    /**
     * Replace the missing breadcrumb menu from BaseController with the standard
     * data for this virtual page.
     */
    protected function ensureBreadcrumbMenu(string $itemKey): void
    {
        if (!empty($this->data['breadcrumbMenu']['items'])) {
            return;
        }

        $this->data['breadcrumbMenu'] = [
            'name'       => 'breadcrumb',
            'identifier' => 'breadcrumb',
            'items'      => [
                'home'   => [
                    'label'   => __('Home', 'municipio'),
                    'href'    => $this->data['homeUrl'],
                    'current' => false,
                    'icon'    => 'home',
                ],
                $itemKey => [
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
    protected function ensurePrintMenuItem(): void
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

    /**
     * Returns the template tags that can be used in the page content.
     */
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
    protected function replaceTemplateTags(string $content): string
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
