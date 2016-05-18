<?php $currentUser = wp_get_current_user(); ?>

<nav class="navbar navbar-sm hidden-print">
    <div class="container">
        <div class="grid">
            <div class="grid-xs-12 {!! apply_filters('Municipio/header_grid_size','grid-md-12'); !!}">
                <div class="grid">
                    <div class="grid-xs-12">
                        <a href="{{ network_home_url() }}" class="site-title pull-left"><h1 class="no-margin no-padding">Helsingborg stads intranät</h1></a>

                        <form class="search" method="get" action="/">
                            <label for="searchkeyword-0" class="sr-only">{{ get_field('search_label_text', 'option') ? get_field('search_label_text', 'option') : __('Search', 'municipio') }}</label>

                            <div class="input-group">
                                <input id="searchkeyword-0" autocomplete="off" class="form-control" type="search" name="s" placeholder="{{ get_field('search_placeholder_text', 'option') ? get_field('search_placeholder_text', 'option') : 'What are you looking for?' }}" value="<?php echo (isset($_GET['s']) && strlen($_GET['s']) > 0) ? urldecode(stripslashes($_GET['s'])) : ''; ?>">
                                <span class="input-group-addon-btn">
                                    <button type="submit" class="btn"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </form>

                        <nav class="pull-right">
                            <ul class="nav nav-horizontal">
                                <li><a href="#">A-Ö</a></li>

                                @if ($currentUser->ID > 0)
                                    @if ( (isset($currentUser->first_name) && !empty($currentUser->first_name)) || (isset($currentUser->last_name) && !empty($currentUser->last_name)) )
                                        <li><a href="#">{{ isset($currentUser->first_name) ? $currentUser->first_name : '' }} {{ isset($currentUser->last_name) ? $currentUser->last_name : '' }}</a></li>
                                    @else
                                        <li><a href="#"><?php _e('Your profile', 'municipio-intranet'); ?></a></li>
                                    @endif
                                @else
                                    <li>
                                        <a href="#" data-dropdown=".login-dropdown"><?php _e('Log in'); ?> <i class="fa fa-caret-down"></i></a>
                                        <div class="dropdown login-dropdown">
                                            <div class="gutter">
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
