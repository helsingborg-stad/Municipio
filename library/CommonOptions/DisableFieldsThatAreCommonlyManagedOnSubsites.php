<?php

namespace Municipio\CommonOptions;

use WpService\WpService;
use AcfService\AcfService;
use Municipio\Helper\SiteSwitcher\SiteSwitcher;
use Municipio\HooksRegistrar\Hookable;
use Municipio\CommonOptions\CommonOptionsConfigInterface;

class DisableFieldGroupsOnSubsites implements Hookable
{
    private array $disabledGroups = [];

    public function __construct(
        private WpService $wpService, 
        private AcfService $acfService, 
        private SiteSwitcher $siteSwitcher, 
        private CommonOptionsConfigInterface $config
    ) {
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('acf/init', [$this, 'disableFieldGroups']);
        $this->wpService->addAction('admin_notices', [$this, 'showDisabledGroupsNotice']);
    }

    /**
     * Disables field groups that are commonly managed on subsites.
     * 
     * @return void
     */
    public function disableFieldGroups(): void
    {
        if (!$this->shouldDisableFieldGroups()) {
            return;
        }

        if ($acfGroupKeysToFilter = $this->config->getAcfFieldGroupsToFilter()) {
            foreach ($acfGroupKeysToFilter as $key => $mainSiteUrl) {
                $this->wpService->addFilter('acf/load_field_group', function ($fieldGroup) use ($key, $mainSiteUrl) {
                    if ($fieldGroup['key'] === $key) {
                        $fieldGroup['active'] = false; // Disables the field group.
                        $fieldGroup['description'] = __('MANAGED FROM MAIN SITE: ', 'municipio') . ($fieldGroup['description'] ?? '');
                        $this->disabledGroups[] = [
                            'title' => $fieldGroup['title'],
                            'url'   => $mainSiteUrl,
                        ]; // Store the title and main site URL for the notice.
                    }
                    return $fieldGroup;
                });
            }
        }
    }

    /**
     * Display a notice about disabled field groups with links to the main site.
     * 
     * @return void
     */
    public function showDisabledGroupsNotice(): void
    {
        if (empty($this->disabledGroups)) {
            return;
        }

        $message = __('The following field groups have been disabled as they are managed from the main site:', 'municipio');
        $message .= '<ul>';
        foreach ($this->disabledGroups as $group) {
            $message .= sprintf(
                '<li><a href="%s" target="_blank">%s</a></li>',
                esc_url($group['url']),
                esc_html($group['title'])
            );
        }
        $message .= '</ul>';

        printf(
            '<div class="notice notice-info is-dismissible"><p>%s</p></div>',
            $message
        );
    }

    /**
     * Check if the field groups should be disabled.
     * 
     * @return bool
     */
    private function shouldDisableFieldGroups(): bool
    {
        return !$this->wpService->isMainSite();
    }
}