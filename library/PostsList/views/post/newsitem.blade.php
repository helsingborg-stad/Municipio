@newsItem([
    'heading'             => $post->getTitle(),
    'content'             => $getExcerpt($post, 55),
    'image'               => $post->getImage(),
    'date'                => [
        'timestamp' => $getDateTimestamp($post),
        'format'    => $getDateFormat(),
    ],
    'readTime'            => $getReadingTime($post),
    'link'                => $post->getPermalink(),
    'context' => ['archive', 'archive.list', 'archive.list.news-item'],
    'hasPlaceholderImage' => $appearanceConfig->shouldDisplayPlaceholderImage() && !$post->getImage(),
])
    @slot('headerLeftArea')
        @if(!empty($getTags($post)))
            @tags([
                'compress' => 4, 
                'tags' => $getTags($post), 
                'format' => false,
            ])
            @endtags
        @endif
    @endslot
@endnewsItem


