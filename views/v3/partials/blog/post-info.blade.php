<ul>
    @if (in_array('author', (array)get_field('archive_' . sanitize_title(get_post_type()) . '_post_display_info', 'option')) && get_field('post_show_author', get_the_id()) !== false )
    <li class="post-author">
        @if (in_array('author_image', (array)get_field('archive_' . sanitize_title(get_post_type()) . '_post_display_info', 'option')) && is_array(get_field('user_profile_picture', 'user_' . get_the_author_meta('ID'))))
            <span class="post-author-image" style="background-image:url('{{ get_field('user_profile_picture', 'user_' . get_the_author_meta('ID'))['url'] }}');"><img src="{{ get_field('user_profile_picture', 'user_' . get_the_author_meta('ID'))['url'] }}" alt="{{ (!empty(get_the_author_meta('first_name')) && !empty(get_the_author_meta('last_name'))) ? get_the_author_meta('first_name') . ' ' . get_the_author_meta('last_name')  : get_the_author() }}"></span>
        @endif

        @if (!empty(get_the_author_meta('first_name')) && !empty(get_the_author_meta('last_name')))
            @if (get_field('page_link_to_author_archive', 'option'))
                <a href="{{ get_author_posts_url(get_the_author_meta('ID')) }}"><span class="post-author-name">{{ get_the_author_meta('first_name') }} {{get_the_author_meta('last_name') }}</span></a>
            @else
                <span class="post-author-name">{{ get_the_author_meta('first_name') }} {{ get_the_author_meta('last_name') }}</span>
            @endif
        @else
            @if (get_field('page_link_to_author_archive', 'option'))
                <a href="{{ get_author_posts_url(get_the_author_meta('ID')) }}"><span class="post-author-name">{{ get_the_author() }}</span></a>
            @else
                <span class="post-author-name">{{ get_the_author() }}</span>
            @endif
        @endif
    </li>
    @endif

    @if ((!is_single() && get_field('archive_' . sanitize_title(get_post_type()) . '_feed_date_published', 'option') != 'false' && !empty(get_field('archive_' . sanitize_title(get_post_type()) . '_feed_date_published', 'option'))) || (is_single() && get_field('archive_' . sanitize_title(get_post_type()) . '_post_date_published', 'option') != 'false') && !empty(get_field('archive_' . sanitize_title(get_post_type()) . '_post_date_published', 'option')))
    <li class="post-date">
        <time>
            @if (is_single())
                {{ in_array(get_field('archive_' . sanitize_title(get_post_type()) . '_post_date_published', 'option'), array('datetime', 'date')) ? the_time(get_option('date_format')) : '' }}
                {{ in_array(get_field('archive_' . sanitize_title(get_post_type()) . '_post_date_published', 'option'), array('datetime', 'time')) ? the_time(get_option('time_format')) : '' }}
            @else
                {{ in_array(get_field('archive_' . sanitize_title(get_post_type()) . '_feed_date_published', 'option'), array('datetime', 'date')) ? the_time(get_option('date_format')) : '' }}
                {{ in_array(get_field('archive_' . sanitize_title(get_post_type()) . '_feed_date_published', 'option'), array('datetime', 'time')) ? the_time(get_option('time_format')) : '' }}
            @endif
        </time>
    </li>
    @endif

    @if (comments_open() && get_option('comment_registration') == 0 || comments_open() && is_user_logged_in())
    <li class="post-comments">
        <a href="{{ comments_link() }}">
            <span class="hidden-md hidden-lg"><i class="fa fa-lg fa-comments"></i> {{ comments_number('0', '1', '%') }}</span>
            <span class="hidden-xs hidden-sm"><?php _e('Comments'); ?> ({{ comments_number('0', '1', '%') }})</span>
        </a>
    </li>
    @endif

    <?php do_action('Municipio/blog/post_info', $post); ?>

    @if (is_single() && is_array($settingItems) && !empty($settingItems))
    <li>
        <a href="#" data-dropdown=".post-settings" class="post-settings-toggle">
            <i class="pricon pricon-menu-dots"></i>
        </a>
        <ul class="post-settings dropdown-menu dropdown-menu-arrow dropdown-menu-arrow-left">
            @foreach($settingItems as $item)
                <li>{!! $item !!}</li>
            @endforeach
        </ul>
    </li>
    @endif
</ul>
