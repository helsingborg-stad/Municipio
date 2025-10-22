@newsItem([
    'heading'             => $post->postTitle,
    'content'             => $post->excerptShort,
    'image'               => $post->image,
    'date'                => $showDate ? [
        'timestamp' => $post->getArchiveDateTimestamp(),
        'format'    => $post->getArchiveDateFormat(),
    ] : null,
    'readTime'            => $post->readingTime,
    'link'                => $post->permalink,
    'context'             => ['module.posts.news-item'],
    'hasPlaceholderImage' => $post->hasPlaceholderImage,
    'classList' => $post->classList ?? [],
    'standing' => $standing,
    'attributeList' => $post->attributeList ?? [],
])
    @slot('headerLeftArea')
        @if (!empty($postsSources) && count($postsSources) > 1 && !empty($post->originalSite))
            @typography([
                'element' => 'span',
                'variant' => 'bold',
                'classList' => ['u-margin__y--0', 'u-padding__right--1'],
            ])
                {{ $post->originalSite }}
            @endtypography
        @endif
        @if($post->termsUnlinked)
            @tags([
                'compress' => 4, 
                'tags' => $post->termsUnlinked, 
                'format' => false,
            ])
            @endtags
        @endif
    @endslot

    @slot('headerRightArea')
        @includeWhen($post->commentCount !== false, 'partials.comment-count')
    @endslot
@endnewsItem
