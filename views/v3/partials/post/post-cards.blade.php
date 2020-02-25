@card([
        'href' => $post->permalink,
        'image' =>  municipio_get_thumbnail_source(null,array(400,225)),
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
