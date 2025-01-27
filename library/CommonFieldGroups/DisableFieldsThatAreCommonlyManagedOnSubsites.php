<?php

namespace Municipio\CommonFieldGroups;

use WpService\WpService;
use AcfService\AcfService;
use Municipio\Helper\SiteSwitcher\SiteSwitcher;
use Municipio\HooksRegistrar\Hookable;
use Municipio\CommonFieldGroups\CommonFieldGroupsConfigInterface;

class DisableFieldsThatAreCommonlyManagedOnSubsites implements Hookable
{
    public function __construct(
        private WpService $wpService,
        private AcfService $acfService,
        private SiteSwitcher $siteSwitcher,
        private CommonFieldGroupsConfigInterface $config
    ) {
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('admin_init', [$this, 'disableFieldGroups']);
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

        $acfGroupKeysToFilter = $this->config->getAcfFieldGroupsToFilter();
        foreach ($acfGroupKeysToFilter as $acfGroupKey) {
            $acfGroupKey = array_pop($acfGroupKey);
            $this->registerFieldFilter($acfGroupKey);
        }
    }

    /**
     * Register the field filter for a specific group key.
     *
     * @param string $acfGroupKey
     * @return void
     */
    private function registerFieldFilter(string $acfGroupKey): void
    {
        $this->wpService->addFilter('acf/prepare_field', function ($field) use ($acfGroupKey) {
            if (!is_array($field)) {
                return $field;
            }
            return $this->processField($field, $acfGroupKey);
        }, 10, 1);
    }

    /**
     * Process a field and determine if it should be disabled or replaced with a notice.
     *
     * @param array $field
     * @param string $acfGroupKey
     * @return array|false
     */
    public function processField(array $field, string $acfGroupKey)
    {
        if (!$this->isFieldInGroup($field, $acfGroupKey)) {
            return $field;
        }

        return $this->createNoticeField($field, $acfGroupKey);
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
    private function createNoticeField(array $field, string $acfGroupKey): array|false
    {
        //Prevent multiple notices for the same group
        static $processedGroupKeys = [];
        if (in_array($acfGroupKey, $processedGroupKeys, true)) {
            return false;
        }
        $processedGroupKeys[] = $acfGroupKey;

        //Get main blog url
        $mainBlogEditUrl = $this->wpService->getAdminUrl(
            $this->wpService->getMainSiteId(),
            $this->getCurrentAdminPageSlug()
        );

        return [
            '_name'        => 'acf_disabled_field',
            'id'           => $field['id'],
            'label'        => $this->wpService->__('Notice', 'municipio'),
            'instructions' => $this->wpService->__('Some settings for this group are only available on the main blog.', 'municipio'),
            'required'     => false,
            'type'         => 'message',
            'key'          => "{$acfGroupKey}_notice",
            'message'      => '<a href="' . $mainBlogEditUrl . '" class="button button-primary">' . $this->wpService->__('Edit settings on main blog', 'municipio') . '</a>',
            'wrapper'      => ['width' => '100%'],
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

        // Retrieve query parameters as a string, if any
        $queryString = http_build_query($_GET);

        // Combine the script and query string
        return $queryString ? "{$script}?{$queryString}" : $script;
    }
}
