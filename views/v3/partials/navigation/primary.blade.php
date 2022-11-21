<nav role="navigation" aria-label="{{ $lang->primaryNavigation }}" class="u-display--none@xs u-display--none@sm u-display--none@md u-print-display--none" id="main-menu">
    @nav([
        'id' => 'menu-primary',
        'items' => $primaryMenuItems,
        'allowStyle' => true,
        'direction' => 'horizontal',
        'classList' => $classList,
        'context' => ['site.header.nav', 'site.header.casual.nav']
    ])
    @endnav
</nav>