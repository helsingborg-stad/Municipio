@if ($navigation['headerTabsMenu'] || $navigation['headerHelpMenu'] || (is_array(get_field('search_display', 'option')) && in_array('header', get_field('search_display', 'option'))) || (!is_front_page() && is_array(get_field('search_display', 'option')) && in_array('header_sub', get_field('search_display', 'option'))))


    @if ($navigation['headerTabsMenu'] || (is_array(get_field('search_display', 'option')) && in_array('header', get_field('search_display', 'option'))) || (!is_front_page() && is_array(get_field('search_display', 'option')) && in_array('header_sub', get_field('search_display', 'option'))))

        {!! $navigation['headerTabsMenu'] !!}

        @if ( (is_array(get_field('search_display', 'option')) && in_array('header', get_field('search_display', 'option'))) || (!is_front_page() && is_array(get_field('search_display', 'option')) && in_array('header_sub', get_field('search_display', 'option'))) )
            @includeIf('partials.search.top-search')
        @endif

    @endif

    @if ($navigation['headerHelpMenu'])
        {!! $navigation['headerHelpMenu'] !!}
    @endif

@endif
