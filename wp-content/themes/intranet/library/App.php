<?php
namespace Intranet;

class App
{
    public function __construct()
    {
        // Basic theme functionality
        new \Intranet\Theme\General();
        new \Intranet\Theme\Enqueue();
        new \Intranet\Theme\Header();
        new \Intranet\Theme\TableOfContents();

        // Admin functionality
        new \Intranet\Admin\NetworkSettings();
        new \Intranet\Admin\AuthorMetaBox();
        new \Intranet\Admin\PasswordResetInstructions();

        // User services
        new \Intranet\User\General();
        new \Intranet\User\Registration();
        new \Intranet\User\Login();
        new \Intranet\User\Profile();
        new \Intranet\User\Subscription();
        new \Intranet\User\TargetGroups();
        new \Intranet\User\AdministrationUnits();
        new \Intranet\User\Systems();
        new \Intranet\User\Responsibilities();
        new \Intranet\User\Skills();
        new \Intranet\User\Data();

        new \Intranet\SearchWp\General();

        // Custom post types
        new \Intranet\CustomPostType\News();

        // Modularity modules
        if (class_exists('\Modularity\Module')) {
            new \Intranet\Module\News();
            new \Intranet\Module\UserLinks();
            new \Intranet\Module\UserSystems();
        }
    }
}
