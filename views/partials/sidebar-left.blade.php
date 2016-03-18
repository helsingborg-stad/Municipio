@if (get_field('nav_sub_enable', 'option') === true)
<aside class="grid-md-4 grid-lg-3 sidebar-left-sidebar">
    {{-- WP navigation --}}
    @if (get_field('nav_sub_type', 'option') === 'wp')
        {!!
            wp_nav_menu(array(
                'theme_location' => 'sidebar-menu',
                'container' => 'nav',
                'container_class' => 'sidebar-menu',
                'container_id' => 'sidebar-menu',
                'menu_class' => 'nav-aside hidden-xs hidden-sm',
                'menu_id' => '',
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
    @if (get_field('nav_sub_type', 'option') === 'auto')
        <?php
        $menu = new \Municipio\Helper\NavigationTree(array(
            'include_top_level' => !empty(get_field('nav_sub_include_top_level', 'option')) ? get_field('nav_sub_include_top_level', 'option') : false,
            'render' => get_field('nav_sub_render', 'option'),
            'depth' => get_field('nav_sub_depth', 'option')
        ));

        if (isset($menu) && $menu->itemCount() > 0) :
        ?>
        <nav id="sidebar-menu">
            <ul class="nav-aside hidden-xs hidden-sm">
                <?php echo $menu->render(); ?>
            </ul>
        </nav>
        <?php endif; ?>
    @endif

    <?php dynamic_sidebar('left-sidebar'); ?>
</aside>
@endif
