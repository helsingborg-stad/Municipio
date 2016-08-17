<div class="creamy creamy-border-bottom hidden-print">
    <div class="container">
        <div class="grid network-header-container">
            <div class="grid-md-8 text-center-xs text-center-sm">
                <div class="network">
                    <button class="current-network network-title" data-dropdown=".network-search-dropdown">
                        {!! (get_option('intranet_short_name')) ? get_option('intranet_short_name') . ' <em>' . get_bloginfo() . '</em>' : get_bloginfo() !!}
                    </button>
                    <div class="dropdown network-search-dropdown">
                        <form class="network-search" method="get" action="{{ home_url() }}">
                            <label for="searchkeyword-0" class="sr-only">{{ get_field('search_label_text', 'option') ? get_field('search_label_text', 'option') : __('Search', 'municipio') }}</label>

                            <div class="input-group">
                                <input data-dropdown-focus id="searchkeyword-0" autocomplete="off" class="form-control" type="search" name="s" placeholder="<?php echo get_field('search_placeholder_text', 'option') ? get_field('search_placeholder_text', 'option') : __('Search networks…', 'municipio-intranet'); ?>">
                                <span class="input-group-addon-btn">
                                    <button type="submit" class="btn"><i class="pricon pricon-search"></i></button>
                                </span>
                            </div>
                        </form>

                        @if (\Intranet\User\Subscription::getSubscriptions() || \Intranet\User\Subscription::getForcedSubscriptions())
                        <div class="network-search-results">
                            <ul class="my-networks">
                                @foreach (\Intranet\User\Subscription::getForcedSubscriptions() as $site)
                                    <li><a href="{{ $site->path }}">{!! $site->name !!}</a></li>
                                @endforeach

                                @if (is_user_logged_in() && \Intranet\User\Subscription::getSubscriptions())
                                    <li class="title"><?php _e('Networks you are following', 'municipio-intranet'); ?></li>
                                    @foreach (\Intranet\User\Subscription::getSubscriptions() as $site)
                                        <li class="network-title"><a href="{{ $site->path }}">{!! municipio_intranet_format_site_name($site) !!}</a></li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                        @endif

                        <a href="{{ network_site_url('sites') }}" class="show-all"><span class="link-item"><?php _e('Show all networks', 'municipio-intranet'); ?></span></a>
                    </div>
                </div>
            </div>

            @if (is_user_logged_in())
            <div class="grid-md-4 text-center-xs text-center-sm text-right-md text-right-lg">
                @if (!is_author() && get_blog_option(get_current_blog_id(), 'intranet_force_subscription') != 'true')
                <button class="btn btn-primary btn-subscribe" data-subscribe="{{ get_current_blog_id() }}">
                    @if (!\Intranet\User\Subscription::hasSubscribed(get_current_blog_id()))
                    <i class="pricon pricon-plus-o"></i> <?php _e('Subscribe', 'municipio-intranet'); ?>
                    @else
                    <i class="pricon pricon-minus-o"></i> <?php _e('Unsubscribe', 'municipio-intranet'); ?>
                    @endif
                </button>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
