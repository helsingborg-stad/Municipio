 @if(!empty($headerData['upperItems']))
    @scope(['name' => ['header-flexible-upper', 'header-flexible', 'header']])
        @header([
            'classList' => array_merge(
                ['c-header--flexible', 'site-header', $customizer->megaMenuMobile ? 'mega-menu-mobile' : '', !empty($headerData['logoScrollShrinkEnabled']) ? 'c-header--logotype-scroll-shrink' : ''],
                $headerData['upperHeader']['classList'],
                isset($classList) ? (array) $classList : [],
                $classes ?? []
            ),
            'id' => 'site-header-flexible-upper',
            'sticky' => $headerData['upperHeader']['sticky'],
            'attributeList' => $headerData['upperHeader']['attributeList'],
            'context' => 'site.header.flexible.upper'
        ])
            <div class="c-header__main-upper-area">
                @foreach (['left', 'center', 'right'] as $alignment)
                    @include('partials.header.components.headerLoop', 
                        [
                            'area' => 'upper', 
                            'key' => 'upperItems', 
                            'align' => $alignment
                        ]
                    )
                @endforeach
            </div>
        @endheader
    @endscope 
@endif