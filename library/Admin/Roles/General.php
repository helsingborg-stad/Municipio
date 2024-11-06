<?php

namespace Municipio\Admin\Roles;

use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\CurrentUserCan;
use WpService\Contracts\HomeUrl;
use WpService\Contracts\IsAdmin;
use WpService\Contracts\ShowAdminBar;
use WpService\Contracts\WpDoingAjax;
use WpService\Contracts\WpRedirect;

class General
{
    private bool $currentUserHasCapabilities;

    public function __construct(private AddFilter&AddAction&CurrentUserCan&ShowAdminBar&IsAdmin&WpDoingAjax&WpRedirect&HomeUrl $wpService)
    {
        $this->wpService->addAction('admin_init', array($this, 'removeUnusedRoles'));
        $this->wpService->addAction('admin_init', array($this, 'addMissingRoles'));

        if (!$this->wpService->currentUserCan('edit_posts')) {
            $this->handleUsersWithoutEditPostsCapability();
        }
    }

    public function handleUsersWithoutEditPostsCapability()
    {
        $this->wpService->showAdminBar(false);

        $this->wpService->addAction('admin_init', function() {
            if ($this->wpService->isAdmin() && !$this->wpService->wpDoingAjax()) {
                $this->wpService->wpRedirect($this->wpService->homeUrl()); // Redirect to homepage or another URL
                exit;
            }
        });
    }

    /**
     * Adds back missing author role
     */
    public function addMissingRoles()
    {
        if (!get_role('author')) {
            add_role(
                'author',
                'Author',
                array(
                    'upload_files'           => true,
                    'edit_posts'             => true,
                    'edit_published_posts'   => true,
                    'publish_posts'          => true,
                    'read'                   => true,
                    'level_2'                => true,
                    'level_1'                => true,
                    'level_0'                => true,
                    'delete_posts'           => true,
                    'delete_published_posts' => true
                )
            );

            delete_option('_author_role_bkp');
        }
    }

    /**
     * Remove unwanted roles
     * @return void
     */
    public function removeUnusedRoles()
    {
        $removeRoles = array(
            'contributor'
        );

        foreach ($removeRoles as $role) {
            if (!get_role($role)) {
                continue;
            }

            update_option('_' . $role . '_role_bkp', get_role('author'));
            remove_role($role);
        }
    }
}
