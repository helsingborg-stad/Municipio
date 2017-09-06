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
        new \Intranet\Theme\Breadcrumb();

        // Admin functionality
        new \Intranet\Admin\PasswordResetInstructions();
        new \Intranet\Admin\Options();
        new \Intranet\Admin\Filters();
        new \Intranet\Admin\Users();
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
            modularity_register_module(
                INTRANET_PATH . 'library/Module/News',
                'News'
            );

            modularity_register_module(
                INTRANET_PATH . 'library/Module/UserLinks',
                'UserLinks'
            );

            modularity_register_module(
                INTRANET_PATH . 'library/Module/UserSystems',
                'UserSystems'
            );

            modularity_register_module(
                INTRANET_PATH . 'library/Module/IncidentList',
                'IncidentList'
            );
        }

        new \Intranet\Api\Wp();
        new \Intranet\Api\News();
        new \Intranet\Api\Search();
    }
}
