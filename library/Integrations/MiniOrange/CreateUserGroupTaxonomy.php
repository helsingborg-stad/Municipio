<?php

namespace Municipio\Integrations\MiniOrange;

use WpService\WpService;
use Municipio\HooksRegistrar\Hookable;
use Municipio\Integrations\MiniOrange\Config\MiniOrangeConfig;

class CreateUserGroupTaxonomy implements Hookable
{
    public function __construct(private WpService $wpService, private MiniOrangeConfig $config)
    {
    }

  /**
   * Add hooks to register the user group taxonomy
   */
    public function addHooks(): void
    {
        $this->wpService->addAction('init', array($this, 'registerUserGroupTaxonomy'));
        $this->wpService->addAction( 'admin_menu', array($this, 'addUserGroup'));
    }

    /**
     * Adds a submenu page for managing user groups.
     *
     * This method adds a submenu page under the "Users" menu in the WordPress admin dashboard.
     * The submenu page is used for managing user groups.
     *
     * @return void
     */
    public function addUserGroup(): void
    {
        add_submenu_page( 'users.php', 'User Groups', 'User Groups', 'edit_users', 'edit-tags.php?taxonomy=user_group' );
    }

  /**
   * Register the user group taxonomy
   *
   * @return void
   */
    public function registerUserGroupTaxonomy(): void
    {
        $taxonomy = $this->config->getUserGroupTaxonomy();

        register_taxonomy(
            $taxonomy,
            'user',
            array(
            'public' => false,
            'show_ui' => true,
            'labels' => array('name' => 'User Groups', 'singular_name' => 'User Group'),
            'capabilities' => array(
                'manage_terms' => 'edit_users',
                'edit_terms'  => 'edit_users',
                'delete_terms' => 'edit_users',
                'assign_terms' => 'edit_users',
            )
            )
        );
    }
}
