<?php

namespace Municipio\UserGroup;

use WpService\WpService;
use Municipio\HooksRegistrar\Hookable;
use Municipio\UserGroup\Config\UserGroupConfigInterface;

/**
 * Display the user group taxonomy link in the admin UI
 */
class DisplayUserGroupTaxonomyLinkInAdminUi implements Hookable
{
    /**
     * Constructor
     */
    public function __construct(private WpService $wpService, private UserGroupConfigInterface $config)
    {
    }

    /**
     * Add the taxonomy link to the admin menu under the Users section
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('admin_menu', array($this, 'addTaxonomyMenuLink'));
    }

    /**
     * Add the link for managing the taxonomy under the Users section in the admin menu
     */
    public function addTaxonomyMenuLink(): void
    {
        //Check if taxonomy is enabled
        if ($this->wpService->getTaxonomy($this->config->getUserGroupTaxonomy()) == false) {
            return;
        }

        // Ensure that the taxonomy appears under the Users menu
        $this->wpService->addSubmenuPage(
            'users.php',
            $this->wpService->__('User Groups', 'municipio'),
            $this->wpService->__('User Groups', 'municipio'),
            'manage_options',
            'edit-tags.php?taxonomy=' . $this->config->getUserGroupTaxonomy()
        );
    }
}
