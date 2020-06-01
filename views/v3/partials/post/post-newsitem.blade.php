@if ($posts)
    @foreach($posts as $post)

        @link([
            'href' => the_permalink()
        ])
           @segment([
                'layout' => 'col-left',
                'title' => $post->postTitle,
                'sub_title' => $post->postDate,
                'text' => $post->postContent ?? $post->postContent,
                'overlay' => 'blur'
            ])

            @if (municipio_get_thumbnail_source(null,array(400,250)))

                    @image([
                        'src'=> municipio_get_thumbnail_source(null,array(400,250)),
                        'alt' => $post->postTitle,
                        'classList' => ['']
                    ])
                    @endimage

                @endif

            @endsegment
        @endlink

    @endforeach
@endif


