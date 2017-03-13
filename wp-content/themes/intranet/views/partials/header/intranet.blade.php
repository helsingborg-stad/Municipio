<nav class="navbar navbar-sm hidden-print">
    <div class="container">
        <div class="grid grid-table">
            <div class="grid-fit-content hidden-xs hidden-sm">
                {!! municipio_intranet_get_logotype('negative', true) !!}
            </div>
            <div class="grid-auto">
                <div class="grid grid-table grid-va-middle">
                    <div class="grid-auto no-padding">
                        <a href="{{ network_home_url() }}" class="h3 site-title inline-block"><span {!! get_field('logotype_tooltip', 'option') ? 'data-tooltip="' . get_field('logotype_tooltip', 'option') . '"' : '' !!}>{{ get_site_option('site_name') }}</span></a>
                    </div>
                    <div class="grid-fit-content no-padding">
                        @include('partials.header.subnav')
                    </div>
                </div>
                <div class="grid grid-table grid-va-middle">
                    <div class="grid-auto no-padding">
                        @include('partials.header.network-selector')
                    </div>
                    <div class="grid-fit-content hidden-xs hidden-sm">
                        <span class="or"><?php _e('or', 'municipio-intranet'); ?></span>
                    </div>
                    <div class="grid-auto no-padding hidden-xs hidden-sm">
                        @include('partials.header.search')
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

@if (strlen($navigation['mobileMenu']) > 0)
    <nav id="mobile-menu" class="nav-mobile-menu nav-toggle nav-toggle-expand {!! apply_filters('Municipio/mobile_menu_breakpoint','hidden-md hidden-lg'); !!} hidden-print">
        @include('partials.mobile-menu')
    </nav>
@endif

@if (isset($show_userdata_guide) && $show_userdata_guide)
    @include('partials.modal.missing-data')
@endif
