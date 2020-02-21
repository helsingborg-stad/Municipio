<?php global $post; ?>

@link([
    'href' => the_permalink()
])
    @if (municipio_get_thumbnail_source(null,array(400,250)))

        @image([
            'src'=> municipio_get_thumbnail_source(null,array(400,250)),
            'alt' => the_title(),
            'classList' => ['box-image-container']
        ])
        @endimage

    @endif


    @typography([
        "variant" => "h3"
    ])
        {{the_title()}}
    @endtypography

    @if (get_field('archive_' . sanitize_title(get_post_type()) . '_feed_date_published', 'option') != 'false')

        {{-- TODO: $post->dateObject  something from them post object - use right var --}}
        @date([
            'action' => 'formatDate',
            'timestamp' =>  $post->dateObject
        ])
        @enddate

    @endif

    {{ the_excerpt() }}

@endlink

