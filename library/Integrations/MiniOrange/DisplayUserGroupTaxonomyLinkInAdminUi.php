<?php 

namespace Municipio\Integrations\MiniOrange;

use WpService\WpService;
use Municipio\HooksRegistrar\Hookable;
use Municipio\Integrations\MiniOrange\Config\MiniOrangeConfig;

class DisplayUserGroupTaxonomyLinkInAdminUi implements Hookable
{
    private string $taxonomySlug;

    public function __construct(private WpService $wpService, private MiniOrangeConfig $config)
    {
        // Get the taxonomy slug from the config
        $this->taxonomySlug = $this->config->getUserGroupTaxonomy();
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
        // Ensure that the taxonomy appears under the Users menu
        $this->wpService->addSubmenuPage(
            'users.php',  // Parent slug (Users menu)
            'User Groups',  // Page title
            'User Groups',  // Menu title
            'manage_options',  // Capability required to access this page
            'edit-tags.php?taxonomy=' . $this->taxonomySlug  // URL to taxonomy management page
        );
    }
}