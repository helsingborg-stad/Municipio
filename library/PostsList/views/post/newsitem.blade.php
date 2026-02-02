@newsItem([
    'heading'             => $post->getTitle(),
    'content'             => $getExcerpt($post),
    'image'               => $post->getImage(),
    'date'                => $getDateFormat() ? [
        'timestamp' => $getDateTimestamp($post),
        'format'    => $getDateFormat(),
    ] : null,
    'readTime'            => $getReadingTime($post),
    'link'                => $post->getPermalink(),
    'context' => ['archive', 'archive.list', 'archive.list.news-item'],
    'hasPlaceholderImage' => $shouldDisplayPlaceholderImage($post),
    'attributeList' => ['data-js-posts-list-item' => true],
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


