@if (!empty($quicklinksMenuItems))

    @if (($customizer->quicklinksLocation == 'frontpage' && $isFrontPage) || $customizer->quicklinksLocation == 'everywhere')
        @header([
            'id' => 'quicklinks-header',
            'classList' => ['site-header', 's-nav-fixed'],
            'context' => ['site.quicklinks']
        ])
            <div class="c-header__menu c-header__menu--secondary u-padding--05 u-print-display--none">
                <div class="o-container">
                    <nav aria-label="{{ $lang->quicklinksNavigation }}">
                        @nav([
                            'id' => 'menu-quicklinks',
                            'items' => $quicklinksMenuItems,
                            'direction' => 'horizontal',
                            'classList' => ['u-flex-wrap@sm', 'u-flex-wrap@xs'],
                            'context' => ['site.quicklinks.nav'],
                            'height' => 'md',
                            'expandLabel' => $lang->expand
                        ])
                        @endnav
                    </nav>
                </div>
            </div>
        @endheader
    @endif
@endif
