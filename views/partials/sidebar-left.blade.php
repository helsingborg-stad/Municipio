<div class="grid-md-4 grid-lg-3">
    <?php
    global $post;
    $childOf = isset(array_reverse(get_post_ancestors($post))[1]) ? array_reverse(get_post_ancestors($post))[1] : get_option('page_on_front');
    $menu = wp_list_pages(array(
        'title_li' => '',
        'child_of' => $childOf,
        'echo'     => 0,
        'walker'   => new \Municipio\Walker\Navigation(),
        'include'  => \Municipio\Helper\Navigation::getNavigationPages($post, 'csv')
    ));

    if ($menu) : ?>
    <nav>
        <a href="#menu-open" id="menu-open" class="hidden-sm hidden-md hidden-lg menu-trigger"><span class="menu-icon"></span></a>
        <ul class="nav-aside hidden-xs">
            <?php echo $menu; ?>
        </ul>
    </nav>
    <?php endif; ?>

    <?php dynamic_sidebar('left-sidebar'); ?>
</div>
