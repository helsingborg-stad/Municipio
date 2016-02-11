<header id="site-header" class="header-casual">
    <nav class="navbar navbar-mainmenu">
        <div class="container">
            <div class="grid">
                <div class="grid-xs-10 {!! apply_filters('Municipio/header_grid_size','grid-md-12'); !!}">

                    {!! municipio_get_logotype(get_field('header_logotype', 'option'), get_field('logotype_tooltip', 'option')) !!}

                    {!!
                        wp_nav_menu(array(
                            'theme_location' => 'main-menu',
                            'container' => false,
                            'container_class' => 'menu-{menu-slug}-container',
                            'container_id' => '',
                            'menu_class' => 'nav nav-horizontal ' . apply_filters('Municipio/desktop_menu_breakpoint', 'hidden-xs hidden-sm'),
                            'menu_id' => 'main-menu',
                            'echo' => false,
                            'before' => '',
                            'after' => '',
                            'link_before' => '',
                            'link_after' => '',
                            'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                            'depth' => 1,
                        ));
                    !!}

                </div>
                <div class="grid-xs-2 {!! apply_filters('Municipio/mobile_menu_breakpoint','hidden-md hidden-lg'); !!} text-right">
                    <a href="#menu-open" id="menu-open" class="menu-trigger"><span class="menu-icon"></span></a>
                </div>
            </div>
        </div>
    </nav>

    <nav id="mobile-menu" class="nav-mobile-menu nav-toggle hidden">
        {!!
            wp_nav_menu(array(
                'theme_location' => 'main-menu',
                'container' => false,
                'container_class' => 'menu-{menu-slug}-container',
                'container_id' => '',
                'menu_class' => 'nav-mobile',
                'menu_id' => '',
                'echo' => false,
                'before' => '',
                'after' => '',
                'link_before' => '',
                'link_after' => '',
                'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                'depth' => -1,
            ));
        !!}
    </nav>
</header>

@if (is_front_page())
    @include('partials.hero')
@endif
