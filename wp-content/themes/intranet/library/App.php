<?php
namespace Intranet;

class App
{
    public function __construct()
    {
        // Data
        new \Intranet\Helper\PostType();
        new \Intranet\Helper\FragmentCache();

        // Basic theme functionality
        new \Intranet\Theme\General();
        new \Intranet\Theme\Enqueue();
        new \Intranet\Theme\Header();
        new \Intranet\Theme\TableOfContents();
        new \Intranet\Theme\Walkthrough();

        // Admin functionality
        new \Intranet\Admin\PasswordResetInstructions();
        new \Intranet\Admin\Options();
        new \Intranet\Admin\Filters();
        new \Intranet\Admin\RemoveSite();

        // User services
        new \Intranet\User\General();
        new \Intranet\User\Registration();
        new \Intranet\User\Login();
        new \Intranet\User\SsoRedirect();
        new \Intranet\User\Profile();
        new \Intranet\User\Subscription();
        new \Intranet\User\TargetGroups();
        new \Intranet\User\AdministrationUnits();
        new \Intranet\User\Systems();
        new \Intranet\User\Responsibilities();
        new \Intranet\User\Skills();
        new \Intranet\User\Data();

        new \Intranet\Search\Elasticsearch();

        // Custom post types
        new \Intranet\CustomPostType\News();
        new \Intranet\CustomPostType\Incidents();

        // Modularity modules
        if (class_exists('\Modularity\Module')) {
            new \Intranet\Module\News();
            new \Intranet\Module\UserLinks();
            new \Intranet\Module\UserSystems();
            new \Intranet\Module\IncidentList();
        }

        new \Intranet\Api\Wp();
        new \Intranet\Api\News();
        new \Intranet\Api\Search();
    }
}
