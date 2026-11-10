<?php

declare(strict_types=1);

namespace Municipio\EasyRead\Migration;

use WpService\WpService;

/** Prevents the deprecated plugin and the built-in feature from running together. */
final class LegacyPluginConflictGuard
{
    private const LEGACY_PLUGIN = 'easy-to-read-alternative/easy-reading.php';

    public function __construct(private WpService $wpService) {}

    public function deactivateConflictingPlugin(): bool
    {
        $this->loadPluginFunctions();

        $isNetworkActive = $this->wpService->isPluginActiveForNetwork(self::LEGACY_PLUGIN);
        if (!$isNetworkActive && !$this->wpService->isPluginActive(self::LEGACY_PLUGIN)) {
            return false;
        }

        $this->wpService->deactivatePlugins(self::LEGACY_PLUGIN, false, $isNetworkActive ? true : null);
        $this->wpService->addAction('admin_notices', [$this, 'renderAdminNotice']);

        return true;
    }

    public function renderAdminNotice(): void
    {
        echo '<div class="notice notice-warning"><p>' . esc_html($this->wpService->__('The Easy Reading plugin was deactivated because its functionality is now built into Municipio.', 'municipio')) . '</p></div>';
    }

    private function loadPluginFunctions(): void
    {
        if (function_exists('is_plugin_active')) {
            return;
        }

        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
}
