<?php

namespace Municipio\UserGroup;

use WpService\WpService;
use Municipio\HooksRegistrar\Hookable;
use Municipio\UserGroup\Config\UserGroupConfigInterface;

/**
 * Display User Group taxonomy in the Users List table
 */
class DisplayUserGroupTaxonomyInUsersList implements Hookable
{
    private string $taxonomy;

    /**
     * Constructor
     */
    public function __construct(private WpService $wpService, private UserGroupConfigInterface $config)
    {
        $this->taxonomy = $this->config->getUserGroupTaxonomy();
    }

    /**
     * Add hooks to modify the Users List table
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('manage_users_columns', array($this, 'addUserGroupColumn'));
        $this->wpService->addAction('manage_users_custom_column', array($this, 'renderUserGroupColumn'), 10, 3);
    }

    /**
     * Add a new column for the User Group taxonomy
     *
     * @param array $columns Existing columns in the Users List
     * @return array Modified columns
     */
    public function addUserGroupColumn(array $columns): array
    {
        $columns['user_group'] = $this->wpService->__('User Group', 'municipio');
        return $columns;
    }

    /**
     * Render content for the User Group column
     *
     * @param string $value     The column content. Default is empty.
     * @param string $column    The name of the column being rendered.
     * @param int    $userId    The ID of the user being displayed.
     * @return string Modified column content.
     */
    public function renderUserGroupColumn(string $value, string $column, int $userId): string
    {
        if ($column === 'user_group') {
            $terms = $this->wpService->wpGetObjectTerms($userId, $this->taxonomy);
            if (!empty($terms) && !$this->wpService->isWpError($terms)) {
                return implode(', ', $this->wpService->wpListPluck($terms, 'name'));
            }
            return $this->wpService->__('No Group Assigned', 'municipio');
        }
        return $value;
    }
}
