<nav class="navbar navbar-sm hidden-print">
    <div class="container">
        <div class="grid">
            <div class="grid-xs-12 {!! apply_filters('Municipio/header_grid_size','grid-md-12'); !!}">
                <div class="grid grid-table grid-va-middle no-padding">
                    <div class="grid-fit-content">
                        <a href="{{ network_home_url() }}" class="logotype">
                            {!! municipio_intranet_get_logotype('negative', true) !!}
                        </a>
                    </div>

                    <div class="grid-auto">
                        <form class="search search-main hidden-xs hidden-sm" method="get" action="{{ home_url() }}">
                            <label for="searchkeyword-top" class="sr-only">{{ get_field('search_label_text', 'option') ? get_field('search_label_text', 'option') : __('Search', 'municipio') }}</label>

                            <div class="input-group">
                                <input id="searchkeyword-top" autocomplete="off" class="form-control" type="search" name="s" placeholder="<?php echo get_field('search_placeholder_text', 'option') ? get_field('search_placeholder_text', 'option') : __('What are you looking for?', 'municipio-intranet'); ?>" value="<?php echo (isset($_GET['s']) && strlen($_GET['s']) > 0) ? urldecode(stripslashes($_GET['s'])) : ''; ?>">
                                <span class="input-group-addon-btn">
                                    <button type="submit" class="btn" data-label="{{ __('Search', 'municipio') }}"><i class="pricon pricon-search pricon-lg"></i></button>
                                </span>
                            </div>

                            {!!
                                municipio_intranet_walkthrough(
                                    'Sök efter innehåll',
                                    '<p>Genom att fylla i en sökfras och klicka på sök så kan du söka efter innehåll i samtliga eller specifika intranät.</p>',
                                    '.search-main'
                                )
                            !!}
                        </form>
                    </div>

                    <div class="grid-fit-content">
                        <nav class="subnav clearfix">
                            <ul class="nav nav-horizontal">
                                <li class="subnav-icon hidden-xs"><a href="?walkthrough" data-tooltip="<?php _e('Start help walkthrough', 'municipio-intranet'); ?>"><i class="pricon pricon-question-o pricon-lg"></i><span class="sr-only"><?php _e('Help', 'municipio-intranet'); ?></span></a></li>
                                <li class="hidden-xs"><a href="{{ municipio_table_of_contents_url() }}"><?php _e('A-Z', 'municipio-intranet'); ?></a></li>

                                @if ($currentUser->ID > 0)
                                    <li>
                                        <a href="#" data-dropdown=".login-dropdown">
                                            @if (get_the_author_meta('user_profile_picture', get_current_user_id()))
                                            <span class="profile-image profile-image-icon inline-block" style="background-image:url('{{ get_the_author_meta('user_profile_picture', get_current_user_id()) }}');"></span>
                                            @endif
                                            <span class="hidden-sm hidden-xs">{{ municipio_intranet_get_first_name($currentUser->ID) }} <i class="pricon pricon-caret-down pricon-xs"></i></span>

                                            {!!
                                                municipio_intranet_walkthrough(
                                                    'Användare',
                                                    '<p>Öppna menyn för att hitta vidare till sidorna där du kan administrera ditt användarkonto.</p>',
                                                    '.subnav',
                                                    'center',
                                                    'right'
                                                )
                                            !!}
                                        </a>

                                        <ul class="dropdown-menu login-dropdown dropdown-menu-arrow dropdown-menu-arrow-right">
                                            <li><a href="{{ municipio_intranet_get_user_profile_url() }}" class="pricon pricon-space-right pricon-user-o"><?php _e('Your profile', 'municipio-intranet'); ?></a></li>
                                            <li><a href="{{ municipio_intranet_get_user_profile_edit_url() }}" class="pricon pricon-space-right pricon-settings"><?php _e('Settings'); ?></a></li>
                                            <li><a href="{{ municipio_intranet_get_user_manage_subscriptions_url() }}" class="pricon pricon-space-right pricon-heart"><?php _e('Subscriptions', 'municipio-intranet'); ?></a></li>
                                            <li class="divider"></li>
                                            <li><a href="{{ wp_logout_url() }}" class="pricon pricon-space-right pricon-standby"><?php _e('Log out'); ?></a></li>
                                        </ul>
                                    </li>
                                @else
                                    <li>
                                        <a href="#" data-dropdown=".login-dropdown" {!! isset($_GET['login']) && $_GET['login'] == 'failed' ? 'class="dropdown-open"' : '' !!}>
                                            <?php _e('Log in'); ?> <i class="fa fa-caret-down"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-arrow-right login-dropdown" {!! isset($_GET['login']) && $_GET['login'] == 'failed' ? 'style="display: block;"' : '' !!}>
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

@include('partials.user.modal-login')
@include('partials.user.modal-password')

@if (strlen($navigation['mobileMenu']) > 0)
    <nav id="mobile-menu" class="nav-mobile-menu nav-toggle nav-toggle-expand {!! apply_filters('Municipio/mobile_menu_breakpoint','hidden-md hidden-lg'); !!} hidden-print">
        @include('partials.mobile-menu')
    </nav>
@endif

@include('partials.network-header')

@if (isset($show_userdata_guide) && $show_userdata_guide)
    @include('partials.modal.missing-data')
@endif
