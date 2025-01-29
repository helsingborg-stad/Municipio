<?php

namespace Municipio\UserGroup;

use Municipio\Helper\SiteSwitcher\SiteSwitcherInterface;
use Municipio\HooksRegistrar\Hookable;
use Municipio\UserGroup\Config\UserGroupConfigInterface;
use WpService\Contracts\{__, AddAction, GetMainSiteId, RegisterTaxonomy, IsMultisite, IsMainSite};

/**
 * Create User Group taxonomy.
 */
class CreateUserGroupTaxonomy implements Hookable
{
    /**
     * Constructor.
     */
    public function __construct(
        private AddAction&RegisterTaxonomy&__&IsMultisite&IsMainSite&GetMainSiteId $wpService,
        private UserGroupConfigInterface $config,
        private SiteSwitcherInterface $siteSwitcher
    ) {
    }

    /**
     * Add hooks to register the user group taxonomy
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('init', array($this, 'registerUserGroupTaxonomy'), 5);
    }

    /**
     * Register the user group taxonomy on the main site if multisite
     *
     * @return void
     */
    public function registerUserGroupTaxonomy(): void
    {
        if (!$this->shouldRegisterTaxonomy()) {
            return;
        }

        $this->siteSwitcher->runInSite(
            $this->wpService->getMainSiteId(),
            function () {
                $this->registerTaxonomy();
            }
        );
    }

    /**
     * Register the taxonomy
     *
     * @return void
     */
    private function registerTaxonomy()
    {
        $this->wpService->registerTaxonomy(
            $this->config->getUserGroupTaxonomy(),
            'user',
            array(
                'label'        => $this->wpService->__('User Groups', 'municipio'),
                'hierarchical' => false,
                'public'       => false,
                'show_ui'      => true,
                'show_in_rest' => false,
                'capabilities' => array(
                    'manage_terms' => 'edit_users',
                    'edit_terms'   => 'edit_users',
                    'delete_terms' => 'edit_users',
                    'assign_terms' => 'edit_users',
                ),
            )
        );
    }

    /**
     * Check if the taxonomy should be registered
     * Registers if: Is not multisite or is multisite and is main site
     * @return bool
     */
    private function shouldRegisterTaxonomy(): bool
    {
        return $this->wpService->isMultisite();
    }
}
