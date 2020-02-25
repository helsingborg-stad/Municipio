{!!
    wp_nav_menu(array(
        'theme_location' => 'header-tabs-menu',
        'container' => 'nav',
        'container_class' => 'hidden-md hidden-lg hidden-print',
        'container_id' => '',
        'menu_class' => 'navbar nav-center navbar-creamy navbar-creamy-inner-shadow nav-horizontal',
        'menu_id' => 'help-menu-top-bar',
        'echo' => 'echo',
        'before' => '',
        'after' => '',
        'link_before' => '',
        'link_after' => '',
        'items_wrap' => '<ul class="%2$s">%3$s</ul>',
        'depth' => 1,
        'fallback_cb' => '__return_false'
    ));
!!}
