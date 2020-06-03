@if ($posts)
    <div class="arcive-news-items">
        @foreach($posts as $post)

            @link([
                'href' => get_the_permalink(),
                'slot' => ' '
            ])

               @segment([
                    'layout' => 'col-left',
                    'title' => $post->postTitle,
                    'sub_title' => $post->excerpt,
                    'height' => 'sm',
                    'overlay' => 'blur'
                ])
                @slot('top')

                <span class="c-segment__top-date"> {{date_i18n('l d F Y', strtotime($post->postDate))}} </span>
                @endslot
                @if (municipio_get_thumbnail_source($post->id,array(400,250)))

                        @image([
                            'src'=> municipio_get_thumbnail_source($post->id, array(400,250)),
                            'alt' => $post->postTitle
                        ])
                        @endimage

                @endif

                @endsegment
            @endlink

        @endforeach
    </div>
@endif


