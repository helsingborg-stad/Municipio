@if ($posts)
    <div class="arcive-news-items o-grid">
        @foreach($posts as $post)
        @newsItem([
                'heading'             => $post->postTitle,
                'content'             => $post->excerpt,
                'image'               => $post->imageContract ?? $post->images['thumbnail16:9'],
                'date'                => $post->postDateFormatted,
                'readTime'            => $post->readingTime,
                'link'                => $post->permalink,
                'context' => ['archive', 'archive.list', 'archive.list.news-item'],
                'hasPlaceholderImage' => $anyPostHasImage && empty($post->images['thumbnail16:9']['src']),
                'classList' => [
                    $gridColumnClass
                ],
                'standing' => $standing
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


