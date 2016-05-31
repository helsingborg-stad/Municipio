<div class="creamy creamy-border-bottom">
    <div class="container">
        <div class="grid gutter gutter-lg gutter-vertical">
            <div class="grid-md-6">
                <div class="network">
                    <button class="current-network network-title" data-dropdown=".network-search-dropdown">
                        {!! (get_option('intranet_short_name')) ? get_option('intranet_short_name') . ' <em>' . get_bloginfo() . '</em>' : get_bloginfo() !!}
                    </button>
                    <div class="dropdown network-search-dropdown">
                        <form class="network-search" method="get" action="">
                            <label for="searchkeyword-0" class="sr-only">{{ get_field('search_label_text', 'option') ? get_field('search_label_text', 'option') : __('Search', 'municipio') }}</label>

                            <div class="input-group">
                                <input data-dropdown-focus id="searchkeyword-0" autocomplete="off" class="form-control" type="search" name="s" placeholder="<?php echo get_field('search_placeholder_text', 'option') ? get_field('search_placeholder_text', 'option') : __('Search networks…', 'municipio-intranet'); ?>" value="<?php echo (isset($_GET['s']) && strlen($_GET['s']) > 0) ? urldecode(stripslashes($_GET['s'])) : ''; ?>">
                                <span class="input-group-addon-btn">
                                    <button type="submit" class="btn"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </form>

                        @if (\Intranet\User\Subscription::getSubscriptions() || \Intranet\User\Subscription::getForcedSubscriptions())
                        <div class="network-search-results">
                            <ul class="my-networks">
                                @foreach (\Intranet\User\Subscription::getForcedSubscriptions() as $site)
                                    <li><a href="{{ $site['path'] }}">{!! $site['name'] !!}</a></li>
                                @endforeach

                                @if (is_user_logged_in() && \Intranet\User\Subscription::getSubscriptions())
                                    <li class="title"><?php _e('Networks you are following', 'municipio-intranet'); ?></li>
                                    @foreach (\Intranet\User\Subscription::getSubscriptions() as $site)
                                        <li class="network-title"><a href="{{ $site['path'] }}">{!! municipio_intranet_format_site_name($site) !!}</a></li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                        @endif

                        <a href="{{ network_site_url('network-sites') }}" class="show-all"><span class="link-item"><?php _e('Show all networks', 'municipio-intranet'); ?></span></a>
                    </div>
                </div>
            </div>

            <div class="grid-md-6 text-right">
                @if (!is_author() && get_blog_option(get_current_blog_id(), 'intranet_force_subscription') != 'true')
                <button class="btn btn-primary" data-subscribe="{{ get_current_blog_id() }}">
                    @if (!\Intranet\User\Subscription::hasSubscribed(get_current_blog_id()))
                    <i class="fa fa-plus-circle"></i> <?php _e('Subscribe', 'municipio-intranet'); ?>
                    @else
                    <i class="fa fa-minus-circle"></i> <?php _e('Unsubscribe', 'municipio-intranet'); ?>
                    @endif
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
