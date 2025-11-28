@newsItem([
    'heading'             => $post->getTitle(),
    'content'             => $post->excerptShort,
    'image'               => $post->getImage(),
    'date'                => $showDate ? [
        'timestamp' => $post->getArchiveDateTimestamp(),
        'format'    => $post->getArchiveDateFormat(),
    ] : null,
    'readTime'            => $post->readingTime,
    'link'                => $post->getPermalink(),
    'context'             => ['module.posts.news-item'],
    'hasPlaceholderImage' => $standing ? false : $post->hasPlaceholderImage,
    'classList' => $post->classList ?? [],
    'standing' => $standing,
    'attributeList' => $post->attributeList ?? [],
])
    @slot('headerLeftArea')
        @if (!empty($getOriginalBlogName($post)))
            @typography([
                'element' => 'span',
                'variant' => 'bold',
                'classList' => ['u-margin__y--0', 'u-padding__right--1'],
            ])
                {{ $getOriginalBlogName($post) }}
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
        @includeWhen($post->getCommentCount() > 0, 'partials.comment-count')
    @endslot
@endnewsItem
