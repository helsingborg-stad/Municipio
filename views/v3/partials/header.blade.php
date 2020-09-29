<header id="site-header" style="overflow-x: hidden;">
    @if (!empty($primaryMenuItems))
        @navbar([
            'logo'      => $logotype->standard['url'],
            'items'     => $primaryMenuItems,
            'sidebar'   => ['trigger' => "js-mobile-sidebar"]
        ])

            {{-- @button([
                'color' => 'default',
                'style' => 'basic',
                'icon' => 'people',
                'size' => 'lg',
                'text' => 'Login',
                'classList' => ['c-button--show-search']
            ])
            @endbutton

            @button([
                'color' => 'default',
                'style' => 'basic',
                'icon' => 'search',
                'size' => 'lg',
                'text' => 'Search',
                'classList' => ['c-button--show-search'],
                'attributeList' => ['data-open' => 'm-search-modal__trigger']
            ])
            @endbutton --}}
        @endnavbar
    @endif

    @sidebar([
    'logo'          => $logotype->standard['url'],
    'items'         => $primaryMenuItems,
    'pageId'        => $pageID,
    'classList'     => [
        'l-docs--sidebar',
        'c-sidebar--fixed',
        'u-visibility--hidden@md',
        'u-visibility--hidden@lg',
        'u-visibility--hidden@xl'
    ],
    'attributeList' => [
        'js-toggle-item'    => 'js-mobile-sidebar',
        'js-toggle-class'   => 'c-sidebar--collapsed'
    ],
    'endpoints'     => [
        'children'          => $homeUrlPath . '/wp-json/municipio/v1/navigation/children',
        'active'            => $homeUrlPath . '/wp-json/municipio/v1/navigation/active'
    ],
])
@endsidebar


    {{-- TAB MENU --}}
    {{-- @includeIf('partials.header.tabs') --}}
    @includeIf('partials.search.search-form')
    {{-- SITE LOGO TYPE --}}
    {{--
    @if (get_field('header_logotype', 'option') === 'negative')
        @includeIf('partials.logo', [
            'logo' => get_field('logotype_negative', 'option'),
            'logoTooltip' => get_field('logotype_tooltip', 'option')
            ])
    @else
        @includeIf('partials.logo', [
            'logo' => get_field('logotype', 'option'),
            'logoTooltip' => get_field('logotype_tooltip', 'option')
            ])
    @endif
    --}}

    {{-- SITE SUB TITLE --}}
    @if (get_field('sub_site_title', 'option') && !empty(get_field('sub_site_title', 'option')))
        @typography([
            "element" => "span",
            "variant" => "h4"
        ])
            {{get_field('sub_site_title', 'option')}}
        @endtypography
    @endif

    {{-- NAVIGATION PRIMARY NAV --}}
    @includeIf('partials.navigation.primary')

    {{-- NAVIGATION MOBILE NAV --}}
    @includeIf('partials.navigation.mobile')

    {{-- NAVIGATION HELPER NAV --}}
    @includeIf('partials.navigation.helper')



  
    {{-- After header body --}}
    @yield('after-header-body')


</header>

{{-- TODO: Segments replace Hero--}}

@includeIf('partials.navigation.helper')

@includeIf('partials.hero')
@includeIf('partials.sidebar', ['id' => 'top-sidebar'])
