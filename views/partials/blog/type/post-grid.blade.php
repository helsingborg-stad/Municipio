<?php global $post; $thumbnail = municipio_get_thumbnail_source($post->ID, array(400, 300)); ?>
<div class="{{ $grid_size }}">
    <a href="{{ the_permalink() }}" class="box box-post-brick">
        @if ($thumbnail)
        <div class="box-image" {!! $thumbnail ? 'style="background-image:url(' . $thumbnail . ');"' : '' !!}>
            <img src="{{ municipio_get_thumbnail_source() }}" alt="{{ the_title() }}">
        </div>
        @endif

        <div class="box-content">
            @if (get_field('blog_feed_show_date', 'option') != 'false')
            <span class="box-post-brick-date">
                <time>
                    {{ in_array(get_field('blog_feed_show_date', 'option'), array('datetime', 'date')) ? the_time(get_option('date_format')) : '' }}
                    {{ in_array(get_field('blog_feed_show_date', 'option'), array('datetime', 'time')) ? the_time(get_option('time_format')) : '' }}
                </time>
            </span>
            @endif

            <h3 class="post-title">{{ the_title() }}</h3>
        </div>
        <div class="box-post-brick-lead">
            {{ the_excerpt() }}
        </div>
    </a>
</div>
