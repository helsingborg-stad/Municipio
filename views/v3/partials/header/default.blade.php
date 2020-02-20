<header id="site-header" class="{{ $headerLayout['classes'] }}">

    {{-- SAERCH MENU --}}
    @includeIf('partials.search.search-form')

    {{-- TAB MENU --}}
    @includeIf('partials.header.tabs-nav')

    {{-- SITE LOGO TYPE --}}
    @includeIf('partials.header.logo')

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
    @includeIf('partials.navigation.primary-nav')

    {{-- NAVIGATION MOBILE NAV --}}
    @includeIf('partials.navigation.mobile-nav')

    {{-- NAVIGATION HELPER NAV --}}
    @includeIf('partials.navigation.helper-nav')


    {{-- TODO: find out ??? keep or drop.--}}
    @if ($navigation['headerTabsMenu'] || $navigation['headerHelpMenu'] || (is_array(get_field('search_display', 'option')) && in_array('header', get_field('search_display', 'option'))) || (!is_front_page() && is_array(get_field('search_display', 'option')) && in_array('header_sub', get_field('search_display', 'option'))))

        @if ($navigation['headerTabsMenu'] || (is_array(get_field('search_display', 'option')) && in_array('header', get_field('search_display', 'option'))) || (!is_front_page() && is_array(get_field('search_display', 'option')) && in_array('header_sub', get_field('search_display', 'option'))))

            {!! $navigation['headerTabsMenu'] !!}

            @if ( (is_array(get_field('search_display', 'option')) && in_array('header', get_field('search_display', 'option'))) || (!is_front_page() && is_array(get_field('search_display', 'option')) && in_array('header_sub', get_field('search_display', 'option'))) )
                @includeIf('partials.search.search-form')
            @endif

        @endif

        @if ($navigation['headerHelpMenu'])
            {!! $navigation['headerHelpMenu'] !!}
        @endif

    @endif

</header>

{{-- TODO: Segments replace Hero--}}


@if (is_active_sidebar('top-sidebar'))
    @php //dynamic_sidebar('top-sidebar'); @endphp
@endif



