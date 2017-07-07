<?php global $post; ?>
<div class="{{ $grid_size }}">
    <a href="{{ the_permalink() }}" class="box box-index box-card-post">
        <div class="box-container" data-equal-item>
            @if (municipio_get_thumbnail_source(null,array(400,225)))
            <div class="box-image ratio-16-9" style="background:url('{{ municipio_get_thumbnail_source(null,array(400,225)) }}') no-repeat center center; background-size: cover;">
                @if (in_array('category', (array)get_field('archive_' . sanitize_title(get_post_type()) . '_post_display_info', 'option')) && isset(get_the_category()[0]->name))
                <span class="label-category label label-theme">{{ get_the_category()[0]->name }}</span>
                @endif
            </div>
            @else
                @if (in_array('category', (array)get_field('archive_' . sanitize_title(get_post_type()) . '_post_display_info', 'option')) && isset(get_the_category()[0]->name))
                <span class="label-category label label-theme">{{ get_the_category()[0]->name }}</span>
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
