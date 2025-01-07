<?php 

namespace Municipio\Integrations\MiniOrange;

use WpService\WpService;
use Municipio\HooksRegistrar\Hookable;
use Municipio\Integrations\MiniOrange\Config\MiniOrangeConfig;

class DisplayUserGroupTaxonomyLinkInAdminUi implements Hookable
{
    public function __construct(private WpService $wpService, private MiniOrangeConfig $config)
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