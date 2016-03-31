<header class="post-header">
    <h1><a href="{{ the_permalink() }}">{{ the_title() }}</a></h1>

    <ul>
        @if (get_field('blog_show_author', 'option'))
        <li class="post-author">
            @if (get_field('blog_show_author_image', 'option') && is_array(get_field('user_profile_picture', 'user_' . get_the_author_meta('ID'))))
                <span class="post-author-image" style="background-image:url('{{ get_field('user_profile_picture', 'user_' . get_the_author_meta('ID'))['url'] }}');"><img src="{{ get_field('user_profile_picture', 'user_' . get_the_author_meta('ID'))['url'] }}" alt="{{ (!empty(get_the_author_meta('first_name')) && !empty(get_the_author_meta('last_name'))) ? get_the_author_meta('first_name') . ' ' . get_the_author_meta('last_name')  : get_the_author() }}"></span>
            @endif

            @if (!empty(get_the_author_meta('first_name')) && !empty(get_the_author_meta('last_name')))
                <span class="post-author-name">{{ get_the_author_meta('first_name') }} {{ get_the_author_meta('last_name') }}</span>
            @else
                <span class="post-author-name">{{ get_the_author() }}</span>
            @endif
        </li>
        @endif

        @if ((!is_single() && get_field('blog_feed_show_date', 'option') != 'false') || (is_single() && get_field('blog_show_date', 'option') != 'false'))
        <li class="post-date">
            <time>
                @if (is_single())
                    {{ in_array(get_field('blog_show_date', 'option'), array('datetime', 'date')) ? the_time(get_option('date_format')) : '' }}
                    {{ in_array(get_field('blog_show_date', 'option'), array('datetime', 'time')) ? the_time(get_option('time_format')) : '' }}
                @else
                    {{ in_array(get_field('blog_feed_show_date', 'option'), array('datetime', 'date')) ? the_time(get_option('date_format')) : '' }}
                    {{ in_array(get_field('blog_feed_show_date', 'option'), array('datetime', 'time')) ? the_time(get_option('time_format')) : '' }}
                @endif
            </time>
        </li>
        @endif

        @if (comments_open())
        <li class="post-comments">
            <a href="{{ comments_link() }}">Kommentarer ({{ comments_number('0', '1', '%') }})</a>
        </li>
        @endif
    </ul>
</header>
