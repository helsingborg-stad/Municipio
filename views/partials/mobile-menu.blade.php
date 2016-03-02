<form class="search" method="get" action="/">
    <label for="searchkeyword-mobile" class="sr-only">{{ get_field('search_label_text', 'option') ? get_field('search_label_text', 'option') : 'Search' }}</label>

    <div class="input-group">
        <input id="searchkeyword-mobile" autocomplete="off" class="form-control" type="search" name="s" placeholder="{{ get_field('search_placeholder_text', 'option') ? get_field('search_placeholder_text', 'option') : 'What are you looking for?' }}" value="<?php echo (isset($_GET['s']) && strlen($_GET['s']) > 0) ? urldecode(stripslashes($_GET['s'])) : ''; ?>">
        <span class="input-group-addon-btn">
            <input type="submit" class="btn btn-primary" value="{{ get_field('search_button_text', 'option') ? get_field('search_button_text', 'option') : 'Search' }}">
        </span>
    </div>
</form>

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
