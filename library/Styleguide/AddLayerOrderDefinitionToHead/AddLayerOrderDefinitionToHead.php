<?php

declare(strict_types=1);


namespace Municipio\Styleguide\AddLayerOrderDefinitionToHead;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;

/**
 * Ensures the global CSS layer order is declared before layered styles are evaluated.
 */
class AddLayerOrderDefinitionToHead implements Hookable
{
    private const STYLE_TAG = '<style>@layer wordpress, generic, elements, objects, components, icons, utilities, theme;</style>';

    /**
     * @param AddAction&AddFilter $wpService WordPress service wrapper.
     */
    public function __construct(
        private AddAction&AddFilter $wpService,
    ) {}

    /**
     * Registers the frontend markup filter and the earliest viable admin head hooks.
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('Municipio\MarkupProcessor', [$this, 'process']);
        $this->wpService->addAction('admin_print_styles', [$this, 'render'], 0);
        $this->wpService->addAction('login_head', [$this, 'render'], 0);
    }

    /**
     * Prepends the layer order declaration to the first head tag in rendered frontend markup.
     *
     * @param string $markup The rendered HTML markup.
     * @return string
     */
    public function process(string $markup): string
    {
        // Add as first child of head
        return preg_replace('/<head(.*?)>/', '<head$1>' . self::STYLE_TAG, $markup, 1);
    }

    /**
     * Prints the shared layer order declaration in admin and login heads.
     */
    public function render(): void
    {
        echo self::STYLE_TAG;
    }
}
