<div class="network">
    <?php
        echo municipio_intranet_walkthrough(
            __('Select intranet', 'municipio-intranet'),
            __('Select which intranet you would like to visit. You can also decide which intranets you would like to follow.', 'municipio-intranet'),
            '.network',
            'center'
        );
    ?>

    <button class="current-network network-title" data-dropdown=".network-search-dropdown">
        <strong class="hidden-xs hidden-md"><?php _e('Select intranet', 'municipio-intranet'); ?>:</strong>
        {!! municipio_intranet_format_site_name(Intranet\Helper\Multisite::getSite(get_current_blog_id()), 'long') !!}
        <span class="current-network-dropdown-arrow"></span>
    </button>

    <div class="network-search-dropdown">
        <form class="network-search hidden-xs" method="get" action="{{ home_url() }}">
            <label for="searchkeyword-0" class="sr-only">{{ get_field('search_label_text', 'option') ? get_field('search_label_text', 'option') : __('Search', 'municipio') }}</label>

            <div class="input-group">
                <input id="searchkeyword-0" autocomplete="off" class="form-control" type="search" name="s" placeholder="<?php echo __('Search for intranets', 'municipio-intranet'); ?>">
                <span class="input-group-addon-btn">
                    <button type="submit" class="btn"><i class="pricon pricon-search"></i></button>
                </span>
            </div>
        </form>

        @if (\Intranet\User\Subscription::getSubscriptions() || \Intranet\User\Subscription::getForcedSubscriptions())
        <div class="network-search-results">
            <ul class="my-networks">
                @foreach (\Intranet\User\Subscription::getForcedSubscriptions() as $site)
                    <li class="network-title"><a href="{{ $site->path }}">{!! municipio_intranet_format_site_name($site) !!}</a></li>
                @endforeach

                @if (is_user_logged_in() && \Intranet\User\Subscription::getSubscriptions())
                    <li class="title"><?php _e('Networks you are following', 'municipio-intranet'); ?></li>
                    @foreach (\Intranet\User\Subscription::getSubscriptions() as $site)
                        <li class="network-title">
                            <a href="{{ $site->path }}">
                                {!! municipio_intranet_format_site_name($site) !!}
                                <button class="btn btn-primary btn-subscribe btn-sm pull-right" data-subscribe="{{ $site->blog_id }}">
                                    @if (!\Intranet\User\Subscription::hasSubscribed($site->blog_id))
                                    <i class="pricon pricon-plus-o"></i> <?php _e('Follow', 'municipio-intranet'); ?>
                                    @else
                                    <i class="pricon pricon-minus-o"></i> <?php _e('Unfollow', 'municipio-intranet'); ?>
                                    @endif
                                </button>
                            </a>
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>
        @endif

        <div class="creamy creamy-border-top gutter gutter-sm">
            <a href="{{ network_site_url('sites') }}" class="btn btn-primary btn-block"><?php _e('Show all networks', 'municipio-intranet'); ?></a>
        </div>
    </div>
</div>

