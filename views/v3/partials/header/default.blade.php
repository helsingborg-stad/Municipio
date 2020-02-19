@extends('partials.header')

@section('before-header-body')
    <div class="search-top {!! apply_filters('Municipio/desktop_menu_breakpoint','hidden-sm'); !!} hidden-print" id="search">
        {{ get_search_form() }}
    </div>
@stop

@section('header-body')
    {!!
        wp_nav_menu(array(
            'theme_location' => 'header-tabs-menu',
            'container' => 'nav',
            'container_class' => 'hidden-md hidden-lg hidden-print',
            'container_id' => '',
            'menu_class' => 'navbar nav-center navbar-creamy navbar-creamy-inner-shadow nav-horizontal',
            'menu_id' => 'help-menu-top-bar',
            'echo' => 'echo',
            'before' => '',
            'after' => '',
            'link_before' => '',
            'link_after' => '',
            'items_wrap' => '<ul class="%2$s">%3$s</ul>',
            'depth' => 1,
            'fallback_cb' => '__return_false'
        ));
    !!}

    <div class="container hidden-print">
            <div class="grid-xs-8 grid-sm-8 grid-md-12 text-left-sm text-left-xs">
                {{-- SITE LOGO TYPE --}}
                @includeIf('partials.header.logo')

            </div>

            @if (strlen($navigation['mobileMenu']) > 0)
                <div class="grid-xs-4 grid-sm-4 text-right-sm text-right-xs {!! apply_filters('Municipio/mobile_menu_breakpoint','hidden-md hidden-lg'); !!}  u-hidden@xl">
                    <a href="#mobile-menu" class=" menu-trigger" data-target="#mobile-menu"><span class="menu-icon"></span> <?php _e('Menu', 'municipio'); ?></a>
                </div>
            @endif


            @if (get_field('sub_site_title', 'option') && !empty(get_field('sub_site_title', 'option')))

                <span class="sub-site-title">{!! get_field('sub_site_title', 'option') !!}</span>

            @endif

            @if ($navigation['headerTabsMenu'] || $navigation['headerHelpMenu'] || (is_array(get_field('search_display', 'option')) && in_array('header', get_field('search_display', 'option'))) || (!is_front_page() && is_array(get_field('search_display', 'option')) && in_array('header_sub', get_field('search_display', 'option'))))


                @if ($navigation['headerTabsMenu'] || (is_array(get_field('search_display', 'option')) && in_array('header', get_field('search_display', 'option'))) || (!is_front_page() && is_array(get_field('search_display', 'option')) && in_array('header_sub', get_field('search_display', 'option'))))
                <div>
                    {!! $navigation['headerTabsMenu'] !!}

                    @if ( (is_array(get_field('search_display', 'option')) && in_array('header', get_field('search_display', 'option'))) || (!is_front_page() && is_array(get_field('search_display', 'option')) && in_array('header_sub', get_field('search_display', 'option'))) )
                        @includeIf('partials.search.top-search')
                    @endif
                </div>
                @endif

                @if ($navigation['headerHelpMenu'])
                {!! $navigation['headerHelpMenu'] !!}
                @endif

            @endif
    </div>

    @if (get_field('sub_site_title', 'option'))
        <span class="sub-site-title-block hidden-lg hidden-xl">{!! get_field('sub_site_title', 'option') !!}</span>
    @endif

    @if (get_field('nav_primary_enable', 'option') === true)
        <nav class="navbar navbar-mainmenu hidden-xs hidden-sm hidden-print {{ get_field('header_sticky', 'option') ? 'sticky-scroll' : '' }} {{ is_front_page() && get_field('header_transparent', 'option') ? 'navbar-transparent' : '' }}">
             {!! $navigation['mainMenu'] !!}
        </nav>

        @if (strlen($navigation['mobileMenu']) > 0)
            <nav id="mobile-menu" class="nav-mobile-menu nav-toggle nav-toggle-expand {!! apply_filters('Municipio/mobile_menu_breakpoint','hidden-md hidden-lg'); !!} hidden-print">
                @includeIf('partials.mobile-menu')
            </nav>
        @endif
    @endif
@stop
