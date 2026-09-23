@if (!empty($headerData['lowerItems']))
    @scope(['name' => ['header-flexible-lower', 'header-flexible', 'header']])
        @header([
            'classList' => array_merge(
                ['c-header--flexible', 'site-header', $customizer->megaMenuMobile ? 'mega-menu-mobile' : '', !empty($headerData['logoScrollShrinkEnabled']) ? 'c-header--logotype-scroll-shrink' : ''],
                $headerData['lowerHeader']['classList'],
                isset($classList) ? (array) $classList : [],
                $classes ?? []
            ),
            'id' => 'site-header-flexible-lower',
            'sticky' => $headerData['lowerHeader']['sticky'],
            'attributeList' => $headerData['lowerHeader']['attributeList'],
            'context' => 'site.header.flexible.lower',
        ])
            <div class="c-header__main-lower-area">
                @foreach (['left', 'center', 'right'] as $alignment) 
                    @include('partials.header.components.headerLoop',
                        [
                            'area' => 'lower', 
                            'key' => 'lowerItems', 
                            'align' => $alignment
                        ]
                    )
                @endforeach
            </div>
        @endheader
    @endscope
@endif