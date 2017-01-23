@if (is_author())
<div class="box box-filled box-author">
    <img src="{{ get_the_author_meta('user_profile_picture') }}" alt="{{ get_the_author_meta('nicename') }}" class="box-image">

    <div class="box-content">
        <div class="author-name">{{ municipio_get_author_full_name() }}</div>
        <div class="author-description">{{ get_the_author_meta('description') }}</div>

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
    </div>
</div>
@endif
