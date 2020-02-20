@if (get_field('nav_primary_enable', 'option') === true)
    <nav
        class="navbar navbar-mainmenu hidden-xs hidden-sm hidden-print {{ get_field('header_sticky', 'option') ? 'sticky-scroll' : '' }} {{ is_front_page() && get_field('header_transparent', 'option') ? 'navbar-transparent' : '' }}">
        {!! $navigation['mainMenu'] !!}
    </nav>

    @if (strlen($navigation['mobileMenu']) > 0)
        <nav id="mobile-menu"
             class="nav-mobile-menu nav-toggle nav-toggle-expand {!! apply_filters('Municipio/mobile_menu_breakpoint','hidden-md hidden-lg'); !!} hidden-print">
            @includeIf('partials.mobile-menu')
        </nav>
    @endif

@endif