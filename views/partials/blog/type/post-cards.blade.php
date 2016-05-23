<?php global $post; ?>
<div class="{{ $grid_size }}">
    <a href="{{ the_permalink() }}" class="box box-card box-card-post box-card-post-equal">
        <div class="box-container">
            @if (municipio_get_thumbnail_source(null,array(400,250)))
            <div class="box-image" style="background-image:url('{{ municipio_get_thumbnail_source(null,array(400,250)) }}');">
                <img src="{{ municipio_get_thumbnail_source(null,array(400,250)) }}">
                @if (in_array('category', (array)get_field('archive_' . sanitize_title(get_post_type()) . '_post_display_info', 'option')) && isset(get_the_category()[0]->name))
                <span class="box-card-post-category label label-theme">{{ get_the_category()[0]->name }}</span>
                @endif
            </div>
            @else
                @if (in_array('category', (array)get_field('archive_' . sanitize_title(get_post_type()) . '_post_display_info', 'option')) && isset(get_the_category()[0]->name))
                <span class="box-card-post-category label label-theme">{{ get_the_category()[0]->name }}</span>
                @endif
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
        </div>
    </a>
</div>
