<header id="site-header" class="site-header">
    <div class="container">
        <div class="grid">
            <div class="grid-md-6 text-center-xs text-center-sm">
                {!! municipio_get_logotype(get_field('header_logotype', 'option'), get_field('logotype_tooltip', 'option'), true, get_field('header_tagline_enable', 'option')) !!}
                <a href="#mobile-menu" class="hidden-md hidden-lg menu-trigger" data-target="#mobile-menu"><span class="menu-icon"></span> Meny</a>
            </div>
            <div class="grid-md-6 text-center-sm text-center-xs text-right">
                <div>
                    {!!
                        wp_nav_menu(array(
                            'theme_location' => 'header-tabs-menu',
                            'container' => 'nav',
                            'container_class' => 'menu-header-tabs',
                            'container_id' => '',
                            'menu_class' => 'nav nav-tabs',
                            'menu_id' => 'help-menu-top',
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
                    @if ( (is_array(get_field('search_display', 'option')) && in_array('header', get_field('search_display', 'option'))) || (!is_front_page() && is_array(get_field('search_display', 'option')) && in_array('header_sub', get_field('search_display', 'option'))) )
                        @include('partials.search.top-search')
                    @endif
                </div>

                {!!
                    wp_nav_menu(array(
                        'theme_location' => 'help-menu',
                        'container' => 'nav',
                        'container_class' => 'menu-help',
                        'container_id' => '',
                        'menu_class' => 'nav nav-help nav-horizontal',
                        'menu_id' => 'help-menu-top',
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
            </div>
        </div>
    </div>

    @if (get_field('nav_primary_enable', 'option') === true)
    <nav class="navbar navbar-mainmenu hidden-xs hidden-sm">
        <div class="container">
            <div class="grid">
                <div class="grid-sm-12">
                    {{-- WP navigation --}}
                    @if (get_field('nav_primary_type', 'option') === 'wp')
                        <?php
                            $navAlign = !empty(get_field('nav_primary_align', 'option')) ? get_field('nav_primary_align', 'option') : 'justify';
                        ?>
                        {!!
                            wp_nav_menu(array(
                                'theme_location' => 'main-menu',
                                'container' => false,
                                'container_class' => 'menu-{menu-slug}-container',
                                'container_id' => '',
                                'menu_class' => 'nav nav-' . $navAlign,
                                'menu_id' => 'main-menu',
                                'echo' => false,
                                'before' => '',
                                'after' => '',
                                'link_before' => '',
                                'link_after' => '',
                                'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                                'fallback_cb' => '__return_false'
                            ));
                        !!}
                    @endif

                    {{-- Automatically generated navigation --}}
                    @if (get_field('nav_primary_type', 'option') === 'auto')
                        <?php
                        $menu = new \Municipio\Helper\NavigationTree(array(
                            'include_top_level' => true,
                            'render' => get_field('nav_primary_render', 'option'),
                            'depth' => get_field('nav_primary_depth', 'option')
                        ));

                        if (isset($menu) && $menu->itemCount() > 0) :
                        ?>
                        <nav>
                            <ul class="nav nav-justify">
                                <?php echo $menu->render(); ?>
                            </ul>
                        </nav>
                        <?php endif; ?>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <nav id="mobile-menu" class="nav-mobile-menu nav-toggle nav-toggle-expand hidden-md hidden-lg">
        @include('partials.mobile-menu')
    </nav>
    @endif
</header>

@include('partials.hero')
