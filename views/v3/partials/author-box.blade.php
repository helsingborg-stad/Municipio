<div class="box box-filled box-author">
    @if (get_the_author_meta('user_profile_picture'))
    <img src="{{ get_the_author_meta('user_profile_picture') }}" alt="{{ get_the_author_meta('nicename') }}" class="box-image">
    @endif

    <div class="box-content">
        <div class="author-name">{{ municipio_get_author_full_name() ? municipio_get_author_full_name() : get_the_author_meta('nicename') }}</div>

        @if (get_the_author_meta('description'))
        <div class="author-description">{{ get_the_author_meta('description') }}</div>
        @endif

        @if (get_the_author_meta('user_facebook_url') || get_the_author_meta('user_twitter_username') || get_the_author_meta('user_instagram_username') || get_the_author_meta('user_linkedin_url'))
        <ul class="nav-horizontal gutter gutter-vertical">
            @if (get_the_author_meta('user_facebook_url'))
            <li><a href="{{ get_the_author_meta('user_facebook_url') }}" data-tooltip="<?php _e('My profile on', 'municipio'); ?> Facebook"><i class="pricon pricon-facebook pricon-lg"></i></a></li>
            @endif

            @if (get_the_author_meta('user_twitter_username'))
            <li><a href="https://twitter.com/{{ get_the_author_meta('user_twitter_username') }}" data-tooltip="<?php _e('My profile on', 'municipio'); ?> Twitter"><i class="pricon pricon-twitter pricon-lg"></i></a></li>
            @endif

            @if (get_the_author_meta('user_instagram_username'))
            <li><a href="https://instagram.com/{{ get_the_author_meta('user_instagram_username') }}" data-tooltip="<?php _e('My profile on', 'municipio'); ?> Instagram"><i class="pricon pricon-instagram pricon-lg"></i></a></li>
            @endif

            @if (get_the_author_meta('user_linkedin_url'))
            <li><a href="{{ get_the_author_meta('user_linkedin_url') }}" data-tooltip="<?php _e('My profile on', 'municipio'); ?> LinkedIn"><i class="pricon pricon-linkedin pricon-lg"></i></a></li>
            @endif
        </ul>
        @endif
    </div>
</div>
