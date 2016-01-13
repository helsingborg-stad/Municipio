<div class="grid-md-4 grid-lg-3">
    <nav>
        <a href="#menu-open" id="menu-open" class="hidden-sm hidden-md hidden-lg menu-trigger"><span class="menu-icon"></span></a>
        <ul class="nav-aside hidden-xs">
            <li><a href="#">Link 1</a></li>
            <li class="has-children"><a href="#">Link 2</a></li>
            <li class="current-node has-children">
                <a href="#">Link 3</a>
                <ul class="sub-menu">
                    <li><a href="#">Sublink 1</a></li>
                    <li class="current"><a href="#">Sublink 2</a></li>
                    <li><a href="#">Sublink 3</a></li>
                </ul>
            </li>
            <li><a href="#">Link 4</a></li>
        </ul>
    </nav>

    <?php dynamic_sidebar('left-sidebar'); ?>
</div>
