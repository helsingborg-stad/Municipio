<?php global $post; ?>

    <a href="{{ the_permalink() }}" class="c-card c-card--action">
        @if (municipio_get_thumbnail_source(null,array(400,225)))
        <img class="c-card__image" src="{{ municipio_get_thumbnail_source(null,array(400,225)) }}">
        @endif
        <div class="c-card__body" data-equal-item>
            <h3 class="c-card__title">{{ the_title() }}</h3>
            @if (get_field('archive_' . sanitize_title(get_post_type()) . '_feed_date_published', 'option') != 'false')
            <time class="c-card__sub o-text-secondary o-text-small">
                {{ in_array(get_field('archive_' . sanitize_title(get_post_type()) . '_feed_date_published', 'option'), array('datetime', 'date')) ? the_time(get_option('date_format')) : '' }}
                {{ in_array(get_field('archive_' . sanitize_title(get_post_type()) . '_feed_date_published', 'option'), array('datetime', 'time')) ? the_time(get_option('time_format')) : '' }}
            </time>
            @endif
            <p class="c-card__text">{{ the_excerpt() }}</p>
        </div>
    </a>

