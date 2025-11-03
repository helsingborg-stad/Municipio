
@newsItem([
    'heading'             => $post->getTitle(),
    'content'             => $getExcerptWithoutLinks($post),
    'image'               => $post->getImage(),
    'date'                => [
        'timestamp' => $post->getArchiveDateTimestamp(),
        'format'    => $post->getArchiveDateFormat(),
    ],
    'readTime'            => $getReadingTime($post),
    'link'                => $post->getPermalink(),
    'context' => ['archive', 'archive.list', 'archive.list.news-item'],
    'hasPlaceholderImage' => $config->shouldDisplayPlaceholderImage() && !$post->getImage(),
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


