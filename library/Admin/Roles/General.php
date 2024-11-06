<?php

namespace Municipio\Admin\Roles;

use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\AddRole;
use WpService\Contracts\CurrentUserCan;
use WpService\Contracts\DeleteOption;
use WpService\Contracts\GetRole;
use WpService\Contracts\HomeUrl;
use WpService\Contracts\IsAdmin;
use WpService\Contracts\RemoveRole;
use WpService\Contracts\ShowAdminBar;
use WpService\Contracts\UpdateOption;
use WpService\Contracts\WpDoingAjax;
use WpService\Contracts\WpRedirect;

class General
{
    public function __construct(private AddFilter&AddAction&CurrentUserCan&ShowAdminBar&IsAdmin&WpDoingAjax&WpRedirect&HomeUrl&GetRole&AddRole&DeleteOption&UpdateOption&RemoveRole $wpService)
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

        $this->wpService->addAction('admin_init', function () {
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
        if (!$this->wpService->getRole('author')) {
            $this->wpService->addRole(
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

            $this->wpService->deleteOption('_author_role_bkp');
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
            if (!$this->wpService->getRole($role)) {
                continue;
            }

            $this->wpService->updateOption('_' . $role . '_role_bkp', $this->wpService->getRole('author'));
            $this->wpService->removeRole($role);
        }
    }
}
