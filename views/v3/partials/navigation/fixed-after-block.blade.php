@if (!empty($quicklinksMenu['items']) && $quicklinksPlacement === 'after_first_block')
    @if (($customizer->quicklinksLocation == 'frontpage' && !empty($isFrontPage)) || $customizer->quicklinksLocation == 'everywhere')
        @header([
            'id' => 'quicklinks-header',
            'classList' => ['site-header', 's-nav-fixed', 'u-padding-0', 'u-print-display--none'],
            'context' => ['site.quicklinks']
        ])
            <div class="c-header__menu c-header__menu--secondary u-padding--05">
                <div class="o-container">
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

                </div>
            </div>
        @endheader
    @endif
@endif
