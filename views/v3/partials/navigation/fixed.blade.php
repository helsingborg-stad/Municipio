@if (!empty($quicklinksMenuItems))
    @if(($customize->quicklinks->quicklinksDisplayLocation == 'frontpage' && $isFrontPage) || ($customize->quicklinks->quicklinksDisplayLocation == 'everywhere'))
        @header([
            'id'                => 'quicklinks-header',
            'classList' => [
                'site-header'
            ],
            'context' => ['site.quicklinks']
        ])
            <div class="c-header__menu c-header__menu--secondary u-padding--05 u-print-display--none">
                <div class="o-container">
                    <nav role="navigation" aria-label="{{ $lang->primaryNavigation }}">
                        @nav([
                            'items' => $quicklinksMenuItems,
                            'direction' => 'horizontal',
                            'classList' => ['u-flex-wrap--no-wrap', 'u-justify-content--space-between'],
                            'context' => ['site.quicklinks.nav']
                        ])
                        @endnav
                    </nav>
                </div>
            </div>
        @endheader
    @endif
@endif