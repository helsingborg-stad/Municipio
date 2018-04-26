<div class="search-top {!! apply_filters('Municipio/desktop_menu_breakpoint','hidden-sm'); !!} hidden-print" id="search">
    <div class="container">
        <div class="grid">
            <div class="grid-sm-12">
                {{ get_search_form() }}
            </div>
        </div>
    </div>
</div>

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
    <div class="grid grid-table grid-va-middle">
        <div class="grid-auto text-center-xs text-center-sm">
            <div class="grid grid-table grid-va-middle no-padding">
                <div class="grid-xs-8 grid-sm-8 grid-md-12 text-left-sm text-left-xs">
                    {!! municipio_get_logotype(get_field('header_logotype', 'option'), get_field('logotype_tooltip', 'option'), true, get_field('header_tagline_enable', 'option')) !!}
                </div>

                @if (strlen($navigation['mobileMenu']) > 0)
                    <div class="grid-xs-4 grid-sm-4 text-right-sm text-right-xs {!! apply_filters('Municipio/mobile_menu_breakpoint','hidden-md hidden-lg'); !!}">
                        <a href="#mobile-menu" class=" menu-trigger" data-target="#mobile-menu"><span class="menu-icon"></span> <?php _e('Menu', 'municipio'); ?></a>
                    </div>
                @endif
            </div>
        </div>

        @if (get_field('sub_site_title', 'option') && !empty(get_field('sub_site_title', 'option')))
        <div class="grid-auto text-center hidden-xs hidden-sm hidden-md">
            <span class="sub-site-title">{!! get_field('sub_site_title', 'option') !!}</span>
        </div>
        @endif

        @if ($navigation['headerTabsMenu'] || $navigation['headerHelpMenu'] || (is_array(get_field('search_display', 'option')) && in_array('header', get_field('search_display', 'option'))) || (!is_front_page() && is_array(get_field('search_display', 'option')) && in_array('header_sub', get_field('search_display', 'option'))))
        <div class="grid-auto text-center-sm text-center-xs text-right hidden-xs hidden-sm">

            @if ($navigation['headerTabsMenu'] || (is_array(get_field('search_display', 'option')) && in_array('header', get_field('search_display', 'option'))) || (!is_front_page() && is_array(get_field('search_display', 'option')) && in_array('header_sub', get_field('search_display', 'option'))))
            <div>
                {!! $navigation['headerTabsMenu'] !!}

                @if ( (is_array(get_field('search_display', 'option')) && in_array('header', get_field('search_display', 'option'))) || (!is_front_page() && is_array(get_field('search_display', 'option')) && in_array('header_sub', get_field('search_display', 'option'))) )
                    @include('partials.search.top-search')
                @endif
            </div>
            @endif

            @if ($navigation['headerHelpMenu'])
            {!! $navigation['headerHelpMenu'] !!}
            @endif
        </div>
        @endif
    </div>
</div>

@if (get_field('sub_site_title', 'option'))
    <span class="sub-site-title-block hidden-lg hidden-xl">{!! get_field('sub_site_title', 'option') !!}</span>
@endif

@if (get_field('nav_primary_enable', 'option') === true)
    <nav class="navbar navbar-mainmenu hidden-xs hidden-sm hidden-print {{ get_field('header_sticky', 'option') ? 'sticky-scroll' : '' }} {{ is_front_page() && get_field('header_transparent', 'option') ? 'navbar-transparent' : '' }}">
        <div class="container">
            <div class="grid">
                <div class="grid-sm-12">
                    {!! $navigation['mainMenu'] !!}
                </div>
            </div>
        </div>
    </nav>

    @if (strlen($navigation['mobileMenu']) > 0)
        <nav id="mobile-menu" class="nav-mobile-menu nav-toggle nav-toggle-expand {!! apply_filters('Municipio/mobile_menu_breakpoint','hidden-md hidden-lg'); !!} hidden-print">
            @include('partials.mobile-menu')
        </nav>
    @endif
@endif
