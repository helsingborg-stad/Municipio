<header id="site-header" class="header-casual">
    <nav class="navbar navbar-mainmenu">
        <div class="container">
            <div class="grid">
                <div class="grid-xs-10 grid-md-12">
                    {!! municipio_get_logotype(get_field('header_logotype', 'option'), get_field('logotype_tooltip', 'option')) !!}

                    {!!
                        wp_nav_menu(array(
                            'theme_location' => 'main-menu',
                            'container' => false,
                            'container_class' => 'menu-{menu-slug}-container',
                            'container_id' => '',
                            'menu_class' => 'nav nav-horizontal hidden-xs hidden-sm',
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
                <div class="grid-xs-2 hidden-md hidden-lg text-right">
                    <nav>
                       <a href="#menu-open" id="menu-open" class="{!! apply_filters('Municipio/mobile_menu_breakpoint','hidden-md hidden-lg'); !!} menu-trigger"><span class="menu-icon"></span></a>
                        {!!
                            wp_nav_menu(array(
                                'theme_location' => 'main-menu',
                                'container' => false,
                                'container_class' => 'menu-{menu-slug}-container',
                                'container_id' => '',
                                'menu_class' => 'nav-mobile hidden',
                                'menu_id' => 'mobile-menu',
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
                </div>
            </div>
        </div>
    </nav>
</header>

@if (is_front_page())
    @include('partials.hero')
@endif
