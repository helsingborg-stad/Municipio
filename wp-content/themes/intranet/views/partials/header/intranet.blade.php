<nav class="navbar navbar-sm hidden-print">
    <div class="container">
        <div class="grid">
            <div class="grid-xs-12 {!! apply_filters('Municipio/header_grid_size','grid-md-12'); !!}">
                <div class="grid">
                    <div class="grid-xs-12">
                        <a href="{{ network_home_url() }}" class="site-title"><h1 class="no-margin no-padding">Helsingborg stads intranät</h1></a>

                        <form class="search" method="get" action="{{ home_url() }}">
                            <label for="searchkeyword-0" class="sr-only">{{ get_field('search_label_text', 'option') ? get_field('search_label_text', 'option') : __('Search', 'municipio') }}</label>

                            <div class="input-group">
                                <input id="searchkeyword-0" autocomplete="off" class="form-control" type="search" name="s" placeholder="<?php echo get_field('search_placeholder_text', 'option') ? get_field('search_placeholder_text', 'option') : __('What are you looking for?', 'municipio-intranet'); ?>" value="<?php echo (isset($_GET['s']) && strlen($_GET['s']) > 0) ? urldecode(stripslashes($_GET['s'])) : ''; ?>">
                                <span class="input-group-addon-btn">
                                    <button type="submit" class="btn"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </form>

                        <nav class="subnav">
                            <ul class="nav nav-horizontal">
                                <li><a href="{{ municipio_table_of_contents_url() }}"><?php _e('Table of contents', 'municipio-intranet'); ?></a></li>

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
                                        <a href="#" data-dropdown=".login-dropdown" {!! isset($_GET['login']) && $_GET['login'] == 'failed' ? 'class="dropdown-open"' : '' !!}><?php _e('Log in'); ?> <i class="fa fa-caret-down"></i></a>
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
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

@include('partials.network-header')
