<?php

namespace Municipio\Admin\Gutenberg;

use AcfService\Contracts\GetField;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\GetPostType;

/**
 * When the Gutenberg editor is activated for all pages, the one-page template
 * is the only template that is available, and it is used by default.
 *
 * The stored template value is never changed. Instead the template is resolved
 * as one-page while the setting is active, so switching the setting back
 * restores the template that was previously selected.
 */
class OnePageTemplateOnly implements Hookable
{
    public const TEMPLATE = 'one-page.blade.php';

    /**
     * Constructor.
     */
    public function __construct(
        private AddAction&AddFilter&ApplyFilters&GetPostType $wpService,
        private GetField $acfService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'registerTemplateFilters'], 1000);
        $this->wpService->addFilter('get_post_metadata', [$this, 'resolveTemplateMeta'], 10, 3);
        $this->wpService->addFilter('block_editor_settings_all', [$this, 'removeDefaultTemplateOption'], 10, 2);
    }

    /**
     * Register filters that limit the available templates to one-page.
     * Runs after \Municipio\Helper\Template::add has registered its templates.
     *
     * @return void
     */
    public function registerTemplateFilters(): void
    {
        foreach ($this->getPostTypes() as $postType) {
            $this->wpService->addFilter('theme_' . $postType . '_templates', [$this, 'limitTemplates'], PHP_INT_MAX);
        }
    }

    /**
     * Only keep the one-page template when it is available.
     *
     * @param array $templates The available templates.
     * @return array
     */
    public function limitTemplates($templates)
    {
        if (!is_array($templates) || !isset($templates[self::TEMPLATE]) || !$this->isActive()) {
            return $templates;
        }

        return [self::TEMPLATE => $templates[self::TEMPLATE]];
    }

    /**
     * Resolve the page template as one-page.
     *
     * @param mixed  $value    The value to return, null to continue as usual.
     * @param int    $objectId The post id.
     * @param string $metaKey  The meta key.
     * @return mixed
     */
    public function resolveTemplateMeta($value, $objectId, $metaKey)
    {
        if ($metaKey !== '_wp_page_template' || $value !== null) {
            return $value;
        }

        if (!in_array($this->wpService->getPostType((int) $objectId), $this->getPostTypes(), true) || !$this->isActive()) {
            return $value;
        }

        return [self::TEMPLATE];
    }

    /**
     * Remove the default template option from the block editor.
     *
     * @param array $settings The block editor settings.
     * @param \WP_Block_Editor_Context $context The block editor context.
     * @return array
     */
    public function removeDefaultTemplateOption($settings, $context)
    {
        $post = $context->post ?? null;

        if (
            !is_a($post, 'WP_Post') ||
            !in_array($post->post_type, $this->getPostTypes(), true) ||
            !isset($settings['availableTemplates'][self::TEMPLATE]) ||
            !$this->isActive()
        ) {
            return $settings;
        }

        $settings['availableTemplates'] = [
            self::TEMPLATE => $settings['availableTemplates'][self::TEMPLATE],
        ];

        return $settings;
    }

    /**
     * Whether the Gutenberg editor is activated for all pages.
     *
     * @return bool
     */
    private function isActive(): bool
    {
        return $this->acfService->getField('gutenberg_editor_mode', 'option') === 'all';
    }

    /**
     * Post types that only should use the one-page template.
     *
     * @return array
     */
    private function getPostTypes(): array
    {
        return (array) $this->wpService->applyFilters('Municipio/Admin/Gutenberg/OnePageTemplateOnly/PostTypes', ['page']);
    }
}
