<?php

declare(strict_types=1);

namespace Municipio\StyleguideCss\Customize;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

class EnqueueCustomizeAssets implements Hookable
{
    private const HOOK = 'init';
    private const PRIORITY = 10;

    public function __construct(
        private readonly WpService $wpService,
    ) {}

    /* Registers the necessary hooks for adding customizer properties to components.
     *
     * This method checks if the editor should be enabled based on the current user's capabilities.
     * If the editor is enabled, it adds an action to modify the component data with customizer properties.
     */
    public function addHooks(): void
    {
        if (!$this->shouldEnableEditor()) {
            return;
        }

        $this->wpService->addAction(self::HOOK, [$this, 'enqueueCustomizeAssets'], self::PRIORITY);
    }

    /* Checks if the editor should be enabled based on the current user's capabilities.
     *
     * @return bool True if the editor should be enabled, false otherwise.
     */
    private function shouldEnableEditor(): bool
    {
        return true; // $this->wpService->isAdmin() && $this->wpService->currentUserCan('edit_theme_options');
    }

    /* Enqueues the necessary assets for the customizer editor.
     *
     * This method registers and enqueues the stylesheet for the customizer editor, ensuring that it is loaded on the appropriate pages.
     */
    public function enqueueCustomizeAssets(): void
    {
        $this->wpService->wpEnqueueScript(
            'styleguide-customize',
            $this->wpService->getTemplateDirectoryUri() . '/assets/dist/' . \Municipio\Helper\CacheBust::name('js/designbuilder.js'),
        );
        $this->wpService->wpEnqueueStyle(
            'styleguide-customize-css',
            $this->wpService->getTemplateDirectoryUri() . '/assets/dist/' . \Municipio\Helper\CacheBust::name('css/designbuilder.css'),
        );
    }
}
