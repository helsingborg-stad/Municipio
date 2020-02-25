
        @link([
            'href' =>  $post->permalink,
        ])

            @if (municipio_get_thumbnail_source(null,array(400,250)))

                @image([
                    'src'=> municipio_get_thumbnail_source(null,array(400,250))
                ])
                @endimage

            @endif

                @typography([
                    'element'=> 'h3',
                    'classList' => ['text-highlight']
                ])
                    {{$post->postTitle}}
                @endtypography

                @if (get_field('archive_' . sanitize_title(get_post_type()) . '_feed_date_published', 'option') != 'false')

                    @date([
                        'action' => 'formatDate',
                        'timestamp' =>  $post->postDate
                    ])
                    @enddate

                @endif

                {{ $post->postExcerpt }}

        @endlink

    @includeIf('partials.post.post-footer')

