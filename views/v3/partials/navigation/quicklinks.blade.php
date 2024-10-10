<nav aria-label="{{ $lang->quicklinksNavigation }}">
    @nav([
        'id' => 'menu-quicklinks',
        'items' => $quicklinksMenu['items'],
        'direction' => 'horizontal',
        'classList' => ['u-flex-wrap@sm', 'u-flex-wrap@xs'],
        'context' => ['site.quicklinks.nav'],
        'height' => 'md',
        'expandLabel' => $lang->expand
    ])
    @endnav
</nav>
