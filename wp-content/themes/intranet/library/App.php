<?php
namespace Intranet;

class App
{
    public function __construct()
    {
        new \Intranet\Theme\Enqueue();
        new \Intranet\Theme\Header();
        new \Intranet\Theme\ProtectedPosts();

        new \Intranet\User\Subscription();

        new \Intranet\Admin\NetworkSettings();
    }
}
