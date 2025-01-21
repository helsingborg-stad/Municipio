<?php

namespace Municipio\CommonOptions;

use WpService\WpService;
use AcfService\AcfService;
use Municipio\Helper\SiteSwitcher\SiteSwitcher;
use Municipio\HooksRegistrar\Hookable;
use Municipio\CommonOptions\CommonOptionsConfigInterface;

class DisableFieldsThatAreCommonlyManagedOnSubsites implements Hookable
{
    public function __construct(
        private WpService $wpService, 
        private AcfService $acfService, 
        private SiteSwitcher $siteSwitcher, 
        private CommonOptionsConfigInterface $config
    ) {
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'disableFieldGroups']);
    }

    /**
     * Disable fields that are commonly managed on subsites.
     * 
     * @return void
     */
    public function disableFieldGroups(): void
    {
        if (!$this->config->getShouldDisableFieldGroups()) {
            return;
        }

        if ($acfGroupKeysToFilter = $this->config->getAcfFieldGroupsToFilter()) {
            foreach ($acfGroupKeysToFilter as $acfGroupKey) {
                $acfGroupKey = array_pop($acfGroupKey);

                // Add a filter to disable fields for this group
                $this->wpService->addFilter('acf/prepare_field', function ($field) use ($acfGroupKey) {
                    return $this->filterFieldForGroup($field, $acfGroupKey);
                }, 10, 1);
            }
        }
    }

    /**
     * Filter a field for a specific group: Add a notice or disable the field.
     *
     * @param array $field
     * @param string $acfGroupKey
     * @return array|false
     */
    private function filterFieldForGroup(array $field, string $acfGroupKey)
    {
        static $processedGroups = [];

        // Ensure we only handle fields with the correct parent
        if (!$this->isFieldInGroup($field, $acfGroupKey)) {
            return $field;
        }

        // Check if we've already added a notice for this group
        if (!in_array($acfGroupKey, $processedGroups, true)) {
            $processedGroups[] = $acfGroupKey;
            return $this->createNoticeField($field, $acfGroupKey);
        }

        // Disable subsequent fields in the group
        return false;
    }

    /**
     * Check if a field belongs to a specific group.
     *
     * @param array $field
     * @param string $acfGroupKey
     * @return bool
     */
    private function isFieldInGroup(array $field, string $acfGroupKey): bool
    {
        return !empty($field['parent']) && $field['parent'] === $acfGroupKey;
    }

    /**
     * Create the notice field.
     *
     * @param array $field
     * @param string $acfGroupKey
     * @return array
     */
    private function createNoticeField(array $field, string $acfGroupKey): array
    {
        $currentAdminPageSlug = $this->getCurrentAdminPageSlug();

        $mainBlogEditUrl = $this->wpService->getAdminUrl(
            $this->wpService->getMainSiteId(),
            $currentAdminPageSlug
        ); 

        return [
            '_name' => 'acf_disabled_field',
            'id' => $field['id'],
            'label' => __('Notice', 'municipio'),
            'instructions' => __('Some settings for this group are only available on the main blog.', 'municipio'),
            'required' => false,
            'type' => 'message',
            'key' => "{$acfGroupKey}_notice",
            'message' => '<a href="' . $mainBlogEditUrl . '" class="button button-primary">' . __('Edit settings on main blog', 'municipio') . '</a>',
            'wrapper' => ['width' => '100%'],
        ];
    }

    /**
     * Get the current admin page slug.
     * 
     * @return string
     */
    private function getCurrentAdminPageSlug(): string
    {
        $script = basename($_SERVER['PHP_SELF']);

        // Get all query parameters (if any) as a query string
        $queryString = http_build_query($_GET);
    
        // Combine the script name with the query string
        $slug = $queryString ? "{$script}?{$queryString}" : $script;
    
        return $slug;
    }
}