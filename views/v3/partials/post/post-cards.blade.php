
@php

    if (municipio_get_thumbnail_source(null,array(400,225))) {
        $image = municipio_get_thumbnail_source(null,array(400,225));
    }



 @endphp

@card([
        'href' => $post->permalink,
        'image' => $post-thumbnail,
        'title' => [
            'text' => $post->postTitle,
            'position' => 'body'
        ],
        'classList' => ['c-card--shadow-on-hover'],
        'byline' => ['text' => $post->postDate, 'position' => 'body'],
        'content' => the_excerpt(),
        'hasRipple' => false
    ])

@endcard
