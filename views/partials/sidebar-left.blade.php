<aside class="grid-md-4 grid-lg-3">
    <?php

    $menu = new \Municipio\Helper\NavigationTree();

    if (isset($menu) && $menu->itemCount() > 0) : ?>
    <nav id="sidebar-menu">
        <ul class="nav-aside hidden-xs hidden-sm">
            <?php echo $menu->render(); ?>
        </ul>
    </nav>
    <?php endif; ?>

    {{ dynamic_sidebar('left-sidebar') }}
</aside>
