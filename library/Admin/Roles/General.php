<?php

namespace Municipio\Admin\Roles;

use WpService\Contracts\AddAction;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\CurrentUserCan;
use WpService\Contracts\HomeUrl;
use WpService\Contracts\IsAdmin;
use WpService\Contracts\ShowAdminBar;
use WpService\Contracts\WpDoingAjax;
use WpService\Contracts\WpRedirect;

/**
 * Class General
 *
 * This class represents the General role in the Admin section of the Municipio theme.
 * It contains methods and properties related to the General role.
 */
class General
{
    public const FILTER_HOOK = 'Municipio/Admin/Roles/General/AllowAccess';

    /**
     * Constructor for the General class.
     */
    public function __construct(private AddAction&CurrentUserCan&ShowAdminBar&isAdmin&WpDoingAjax&WpRedirect&HomeUrl&ApplyFilters $wpService)
    {
        $allowAccess = $this->wpService->applyFilters(self::FILTER_HOOK, false);
        
        if (!$this->wpService->currentUserCan('edit_posts') && $allowAccess === false) {
            $this->handleUsersWithoutEditPostsCapability();
        }
    }

    /**
     * Handles users without the edit_posts capability.
     *
     * This method is responsible for handling users who do not have the edit_posts capability. It performs the following actions:
     * - Hides the admin bar for the user.
     * - Adds an action to the 'admin_init' hook to redirect the user to the home URL if they are an admin and not making an AJAX request.
     *
     * @return void
     */
    public function handleUsersWithoutEditPostsCapability()
    {
        $this->wpService->showAdminBar(false);

        $this->wpService->addAction('admin_init', function () {
            if ($this->wpService->isAdmin() && !$this->wpService->wpDoingAjax()) {
                $this->wpService->wpRedirect($this->wpService->homeUrl());
                exit;
            }
        });
    }
}
