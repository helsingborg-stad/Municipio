@if (!empty($quicklinksMenu['items']) && $quicklinksPlacement !== 'after_first_block')
    @if (($customizer->quicklinksLocation == 'frontpage' && $isFrontPage) || $customizer->quicklinksLocation == 'everywhere')
        @scope(['name' => ['quicklinks-header', 'header', 'nav-fixed', 'nav-fixed-top']])
            @header([
                'id' => 'quicklinks-header',
                'classList' => ['s-nav-fixed'],
                'context' => ['site.quicklinks'],
            ])
                <div class="c-header__menu c-header__menu--secondary u-padding--05 u-print-display--none">
                    <div class="o-container">
                        @include('partials.navigation.quicklinks')
                    </div>
                </div>
            @endheader
        @endscope
    @endif
@endif
