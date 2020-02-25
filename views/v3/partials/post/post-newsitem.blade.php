@link([
    'href' => the_permalink()
])
    @if (municipio_get_thumbnail_source(null,array(400,250)))

        @image([
            'src'=> municipio_get_thumbnail_source(null,array(400,250)),
            'alt' => $post->postTitle,
            'classList' => ['']
        ])
        @endimage

    @endif


    @typography([
        "variant" => "h3"
    ])
        {{$post->postTitle}}
    @endtypography

    @if (get_field('archive_' . sanitize_title(get_post_type()) . '_feed_date_published', 'option') != 'false')

        @date([
            'action' => 'formatDate',
            'timestamp' =>  $post->dateObject
        ])
        @enddate

    @endif

    {{ $post->postExcerpt }}

@endlink

