{{-- Deprecated in favor of the PostsList feature --}}
@if ($posts)
    <div class="arcive-news-items o-grid u-align-items--start">
        @foreach($posts as $post)
        @newsItem([
                'heading'             => $post->postTitle,
                'content'             => \Municipio\Helper\Sanitize::sanitizeATags($post->excerpt),
                'image'               => $post->imageContract ?? $post->images['thumbnail16:9'],
                'date'                => [
                    'timestamp' => $post->getArchiveDateTimestamp(),
                    'format'    => $post->getArchiveDateFormat(),
                ],
                'readTime'            => $post->readingTime,
                'link'                => $post->permalink,
                'context' => ['archive', 'archive.list', 'archive.list.news-item'],
                'hasPlaceholderImage' => $anyPostHasImage && empty($post->images['thumbnail16:9']['src']),
                'classList' => explode(' ', $gridColumnClass),
            ])
                @slot('headerLeftArea')
                    @if($post->termsUnlinked)
                        @tags([
                            'compress' => 4, 
                            'tags' => $post->termsUnlinked, 
                            'format' => false,
                        ])
                        @endtags
                    @endif
                @endslot
            @endnewsItem
        @endforeach
    </div>
@endif


