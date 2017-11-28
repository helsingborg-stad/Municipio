<div class="search-top {!! apply_filters('Municipio/desktop_menu_breakpoint', 'hidden-sm'); !!} hidden-print" id="search">
    <div class="container">
        <div class="grid">
            <div class="grid-sm-12">
                {{ get_search_form() }}
            </div>
        </div>
    </div>
</div>

<nav class="navbar navbar-mainmenu hidden-print {{ is_front_page() && get_field('header_transparent', 'option') ? 'navbar-transparent' : '' }} {{ get_field('header_sticky', 'option') ? apply_filters( 'Municipio/StickyScroll', 'sticky-scroll' ) : '' }}">
    <div class="container">
        <div class="grid">
            <div class="grid-xs-12 {!! apply_filters('Municipio/header_grid_size', 'grid-md-12'); !!}">
                <div class="grid">
                    <div class="{{ get_field('header_centered', 'option') ? 'grid-md-12' : 'grid-sm-12 grid-md-4' }}">
                        {!! municipio_get_logotype(get_field('header_logotype', 'option'), get_field('logotype_tooltip', 'option')) !!}

                        @if (strlen($navigation['mobileMenu']) > 0)
                        <a href="#mobile-menu" data-target="#mobile-menu" class="{!! apply_filters('Municipio/mobile_menu_breakpoint', 'hidden-md hidden-lg'); !!} menu-trigger"><span class="menu-icon"></span></a>
                        @endif
                    </div>

                    @if (get_field('nav_primary_enable', 'option') === true)
                    <div class="{{ get_field('header_centered', 'option') ? 'grid-md-12' : 'grid-md-8 text-right' }} {!! apply_filters('Municipio/desktop_menu_breakpoint', 'hidden-xs hidden-sm'); !!}">
                        <nav class="{!! apply_filters('Municipio/Jumbo/NavGroupClass', 'nav-group-overflow'); !!}" data-btn-width="100">
                            {!! $navigation['mainMenu'] !!}

                            <span class="dropdown">
                                <span class="btn btn-primary dropdown-toggle hidden"><?php _e('More', 'municipio'); ?></span>
                                <ul class="dropdown-menu nav-grouped-overflow hidden"></ul>
                            </span>

                            @if (get_field('header_dropdown_links', 'option') === true && \Municipio\Helper\Navigation::getMenuNameByLocation('dropdown-links-menu'))
                                <span class="dropdown">
                                    <span class="btn btn-primary dropdown-toggle dropdown-links-menu">{{ \Municipio\Helper\Navigation::getMenuNameByLocation('dropdown-links-menu')}}</span>
                                    {!! \Municipio\Theme\Navigation::outputDropdownLinksMenu() !!}
                                </span>
                            @endif
                        </nav>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</nav>

@if (strlen($navigation['mobileMenu']) > 0)
    <nav id="mobile-menu" class="nav-mobile-menu nav-toggle-expand nav-toggle {!! apply_filters('Municipio/mobile_menu_breakpoint', 'hidden-md hidden-lg'); !!} hidden-print">
        @include('partials.mobile-menu')
    </nav>
@endif
