<div class="grid-md-4 grid-lg-3">
    <?php
    global $post;
    $menu = wp_list_pages(array(
        'title_li' => '',
        'echo'     => 0,
        'walker'   => new \Municipio\Walker\MainMenu(),
        'include'  => \Municipio\Helper\Navigation::getSidebarNavigationPages($post, 'csv')
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
