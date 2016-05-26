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

        // Admin functionality
        new \Intranet\Admin\NetworkSettings();
        new \Intranet\Admin\AuthorMetaBox();

        // User services
        new \Intranet\User\Registration();
        new \Intranet\User\Login();
        new \Intranet\User\Profile();
        new \Intranet\User\Subscription();
        new \Intranet\User\TargetGroups();

        // Custom post types
        new \Intranet\CustomPostType\News();

        // Modularity modules
        new \Intranet\Module\News();
    }
}
