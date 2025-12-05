<?php

namespace Municipio\Admin;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

class AutoUpdate implements Hookable
{
    public function __construct(private WpService $wpService) {}

    public function addHooks(): void
    {
        $this->removeVersionCheckActions();
        $this->filterDefaultUpdateSettings();
        $this->removeUpdateNags();
    }

    /**
     * Remove default WordPress version check actions
     *
     * @return void
     */
    public function removeVersionCheckActions(): void
    {
        $this->wpService->removeAction('wp_version_check', 'wp_version_check');
        $this->wpService->removeAction('admin_init', '_maybe_update_core');
        $this->wpService->removeAction('admin_init', '_maybe_update_plugins');
        $this->wpService->removeAction('admin_init', '_maybe_update_themes');
    }

    /**
     * Filter default update settings to disable automatic updates
     *
     * @return void
     */
    public function filterDefaultUpdateSettings(): void
    {
        $this->wpService->addFilter('auto_update_core_major', '__return_false');
        $this->wpService->addFilter('auto_update_core_minor', '__return_false');
        $this->wpService->addFilter('auto_update_plugin', '__return_false');
        $this->wpService->addFilter('auto_update_theme', '__return_false');

        // Disable background updater entirely
        $this->wpService->addFilter('automatic_updater_disabled', '__return_true');
    }

    /**
     * Remove update nags from admin area
     *
     * @return void
     */
    public function removeUpdateNags(): void
    {
        $this->wpService->addAction('admin_head', function () {
            $this->wpService->removeAction('admin_notices', 'update_nag', 3);
        });
    }
}