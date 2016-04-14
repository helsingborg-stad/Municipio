<?php global $post; ?>
<div class="post">
<a href="{{ the_permalink() }}" class="box box-news box-news-horizontal">
    @if (municipio_get_thumbnail_source())
    <div class="box-image-container">
        <img src="{{ municipio_get_thumbnail_source() }}">
    </div>
    @endif

    <div class="box-content">
        <h3 class="text-highlight">{{ the_title() }}</h3>

        @if (get_field('archive_' . sanitize_title(get_post_type()) . '_feed_date_published', 'option') != 'false')
        <time>
            {{ in_array(get_field('archive_' . sanitize_title(get_post_type()) . '_feed_date_published', 'option'), array('datetime', 'date')) ? the_time(get_option('date_format')) : '' }}
            {{ in_array(get_field('archive_' . sanitize_title(get_post_type()) . '_feed_date_published', 'option'), array('datetime', 'time')) ? the_time(get_option('time_format')) : '' }}
        </time>
        @endif

        {{ the_excerpt() }}
    </div>
</a>
</div>
