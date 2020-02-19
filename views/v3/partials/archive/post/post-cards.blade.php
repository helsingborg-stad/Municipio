<?php global $post; ?>
{{-- TODO:  Temporary - Maybe move logic to some Controller --}}
@php

    if (municipio_get_thumbnail_source(null,array(400,225))) {
        $image = municipio_get_thumbnail_source(null,array(400,225));
    }

    if (get_field('archive_' . sanitize_title(get_post_type()) . '_feed_date_published',
    'option') != 'false') {

        $publishedDate = in_array(get_field('archive_' . sanitize_title(get_post_type()) .
        '_feed_date_published','option'), array('datetime', 'date')) ?
            the_time(get_option('date_format')) : '';

        $publishedTime = in_array(get_field('archive_' . sanitize_title(get_post_type()) .
                '_feed_date_published', 'option'), array('datetime', 'time')) ?
                the_time(get_option('time_format')) : '';
    }

 @endphp

@card([
        'href' => the_permalink(),
        'image' => $image,
        'title' => [
            'text' => the_title(),
            'position' => 'body'
        ],
        'classList' => ['c-card--shadow-on-hover'],
        'byline' => ['text' => $publishedDate . '' . $publishedTime, 'position' => 'body'],
        'content' => the_excerpt(),
        'hasRipple' => false
    ])

@endcard
