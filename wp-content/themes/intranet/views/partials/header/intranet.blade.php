<nav class="navbar navbar-sm hidden-print">
    <div class="container">
        <div class="grid">
            <div class="grid-xs-12 {!! apply_filters('Municipio/header_grid_size','grid-md-12'); !!}">
                <div class="grid">
                    <div class="grid-xs-12">
                        <a href="{{ network_home_url() }}" class="site-title"><span class="h1 no-margin no-padding">{{ get_site_option('site_name') }}</span></a>

                        <form class="search search-main hidden-xs hidden-sm" method="get" action="{{ home_url() }}">
                            <label for="searchkeyword-top" class="sr-only">{{ get_field('search_label_text', 'option') ? get_field('search_label_text', 'option') : __('Search', 'municipio') }}</label>

                            <div class="input-group">
                                <input id="searchkeyword-top" autocomplete="off" class="form-control" type="search" name="s" placeholder="<?php echo get_field('search_placeholder_text', 'option') ? get_field('search_placeholder_text', 'option') : __('What are you looking for?', 'municipio-intranet'); ?>" value="<?php echo (isset($_GET['s']) && strlen($_GET['s']) > 0) ? urldecode(stripslashes($_GET['s'])) : ''; ?>">
                                <span class="input-group-addon-btn">
                                    <button type="submit" class="btn" data-label="{{ __('Search', 'municipio') }}"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </form>

                        <nav class="subnav clearfix">
                            <ul class="nav nav-horizontal">
                                <li><a href="{{ municipio_table_of_contents_url() }}"><?php _e('A-Z', 'municipio-intranet'); ?></a></li>

                                @if ($currentUser->ID > 0)
                                    <li>
                                        <a href="#" data-dropdown=".login-dropdown">{{ municipio_intranet_get_user_full_name($currentUser->ID) }} <i class="fa fa-caret-down"></i></a>

                                        <div class="dropdown login-dropdown">
                                            <ul class="nav">
                                                <li><a href="{{ municipio_intranet_get_user_profile_url() }}"><?php _e('Your profile', 'municipio-intranet'); ?></a></li>
                                                <li><a href="{{ municipio_intranet_get_user_profile_edit_url() }}"><?php _e('Settings'); ?></a></li>
                                                <li><a href="{{ municipio_intranet_get_user_manage_subscriptions_url() }}"><?php _e('Manage subscriptions', 'municipio-intranet'); ?></a></li>
                                                <li class="divider"></li>
                                                <li><a href="{{ wp_logout_url() }}"><?php _e('Log out'); ?></a></li>
                                            </ul>
                                        </div>
                                    </li>
                                @else
                                    <li>
                                        <a href="#" data-dropdown=".login-dropdown" {!! isset($_GET['login']) && $_GET['login'] == 'failed' ? 'class="dropdown-open"' : '' !!}>
                                            <?php _e('Log in'); ?> <i class="fa fa-caret-down"></i>
                                        </a>
                                        <div class="dropdown login-dropdown" {!! isset($_GET['login']) && $_GET['login'] == 'failed' ? 'style="display: block;"' : '' !!}>
                                            <div class="gutter">
                                                @if (isset($_GET['login']) && $_GET['login'] == 'failed')
                                                <div class="gutter gutter-bottom"><div class="notice notice-sm danger"><?php _e('Login failed. Please try again.', 'municipio-intranet'); ?></div></div>
                                                @endif

                                                @include('partials.user.loginform')
                                            </div>
                                        </div>
                                    </li>
                                @endif

                                <li class="{!! apply_filters('Municipio/mobile_menu_breakpoint','hidden-md hidden-lg'); !!}">
                                    @if (strlen($navigation['mobileMenu']) > 0)
                                        <a href="#mobile-menu" class=" menu-trigger" data-target="#mobile-menu"><span class="menu-icon"></span> <?php _e('Menu', 'municipio'); ?></a>
                                    @endif
                                </li>
                            </ul>
                        </nav>
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

@include('partials.network-header')

@if (isset($show_userdata_guide) && $show_userdata_guide)
    @include('partials.modal.missing-data')
@endif
