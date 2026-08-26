<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Migration;

use Municipio\SearchIndex\SearchPage\Migration\LegacySearchPageActivationMigration;
use WpService\WpService;

/**
 * Deactivates legacy plugins that duplicate the theme's SearchIndex feature.
 */
class LegacyPluginConflictGuard
{
    private const LEGACY_SEARCH_PAGE_PLUGIN = 'algolia-index-js-searchpage-addon/algolia-index-js-searchpage.php';

    private const LEGACY_PLUGINS = [
        'algolia-index/algolia-index.php',
        'algolia-index-typesense-provider/algolia-index-typesense-provider.php',
        self::LEGACY_SEARCH_PAGE_PLUGIN,
        'algolia-index-modularity-addon/algolia-index-modularity-addon.php',
    ];

    public function __construct(private WpService $wpService) {}

    /**
     * Deactivate active legacy plugins and register an admin notice.
     *
     * @return bool True when a legacy plugin was active in this request.
     */
    public function deactivateConflictingPlugins(): bool
    {
        $this->loadPluginFunctions();
        $activePlugins = array_values(array_filter(self::LEGACY_PLUGINS, fn(string $plugin): bool => $this->wpService->isPluginActive($plugin) || $this->wpService->isPluginActiveForNetwork($plugin)));

        if ($activePlugins === []) {
            return false;
        }

        if (in_array(self::LEGACY_SEARCH_PAGE_PLUGIN, $activePlugins, true)) {
            $this->wpService->updateOption(LegacySearchPageActivationMigration::LEGACY_ACTIVATION_OPTION, true, false);
        }

        $this->wpService->deactivatePlugins($activePlugins, false, null);
        $this->wpService->addAction('admin_notices', [$this, 'renderAdminNotice']);

        return true;
    }

    /**
     * Explain why legacy search plugins were deactivated.
     */
    public function renderAdminNotice(): void
    {
        echo '<div class="notice notice-warning"><p>' . esc_html($this->wpService->__('Legacy search index plugins were deactivated because Municipio Search Index now provides this functionality.', 'municipio')) . '</p></div>';
    }

    /**
     * Load WordPress plugin activation helpers when serving non-admin requests.
     */
    private function loadPluginFunctions(): void
    {
        if (function_exists('is_plugin_active')) {
            return;
        }

        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
}