<?php global $post; ?>
<div class="post post-compressed">

        @link([
            'href' =>  the_permalink(),
            'classList' => [
                'box',
                'box-news',
                'box-news-horizontal'
            ]
        ])

            @if (municipio_get_thumbnail_source(null,array(400,250)))
                <div class="box-image-container">
                    @image([
                        'src'=> municipio_get_thumbnail_source(null,array(400,250))
                    ])
                    @endimage
                </div>
            @endif

            <div class="box-content">

                @typography([
                    'element'=> 'h3',
                    'classList' => ['text-highlight']
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

            </div>
        @endlink

    @includeIf('partials.blog.post-footer')
</div>
