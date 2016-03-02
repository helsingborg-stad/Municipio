<header id="site-header" class="site-header header-casual">
    <div class="search-top" id="search">
        <div class="container">
            <div class="grid">
                <div class="grid-sm-12">
                    {{ get_search_form() }}
                </div>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-mainmenu">
        <div class="container">
            <div class="grid">
                <div class="grid-xs-12 {!! apply_filters('Municipio/header_grid_size','grid-md-12'); !!}">

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

                    <a href="#mobile-menu" data-target="#mobile-menu" class="{!! apply_filters('Municipio/mobile_menu_breakpoint','hidden-md hidden-lg'); !!} menu-trigger"><span class="menu-icon"></span></a>
                </div>
            </div>
        </div>
    </nav>

    <nav id="mobile-menu" class="nav-mobile-menu nav-toggle">
        <ul class="nav-mobile">
        <?php
            global $post;
            global $childOf;

            $childOf = isset(array_reverse(get_post_ancestors($post))[0]) ? array_reverse(get_post_ancestors($post))[0] : $post->ID;

            if ($childOf == get_option('page_on_front')) {
                $childOf = null;
            }

            //List pages
            $menu = wp_list_pages(array(
                'title_li' => '',
                'sort_column' => 'menu_order, post_title',
                'sort_order' => 'asc',
                'echo'     => 0,
                'walker'   => new \Municipio\Walker\NavigationMobile(),
                'include'  => \Municipio\Helper\Navigation::getNavigationPages($childOf, 'csv')
            ));

            echo $menu;
        ?>
        </ul>
    </nav>
</header>

@include('partials.hero')
