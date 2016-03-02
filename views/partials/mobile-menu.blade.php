{!! get_search_form() !!}

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
