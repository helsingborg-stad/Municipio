<?php
namespace Intranet;

class App
{
    public function __construct()
    {
        // Basic theme functionality
        new \Intranet\Theme\Enqueue();
        new \Intranet\Theme\Header();
        new \Intranet\Theme\ProtectedPosts();

        // Admin functionality
        new \Intranet\Admin\NetworkSettings();

        // User services
        new \Intranet\User\RecursiveRegistration();
        new \Intranet\User\Login();
        new \Intranet\User\Profile();
        new \Intranet\User\Subscription();
    }
}
