@segment([
    'layout' => 'card',
    'title' => $post->getTitle(),
    'context' => ['module.posts.segment'],
    'tags' => $post->termsUnlinked,
    'image' => $post->getImage() ?? false,
    'date'  => $showDate ? [
        'timestamp' => $getPostDateTimestamp($post),
        'format'    => $post->getArchiveDateFormat(),
    ] : false,
    'content' => $post->excerptShort,
    'buttons' => [['text' => $lang['readMore'], 'href' => $post->getPermalink(), 'color' => 'primary']],
    'containerAware' => true,
    'reverseColumns' => !empty($imagePosition),
    'icon' => $post->getIcon() ? [
        'icon' => $post->getIcon()->getIcon(),
        'color' => 'white',
    ] : false,
    'iconBackgroundColor' => $post->getIcon() ? $post->getIcon()->getCustomColor() : null,
    'hasPlaceholder' => $post->hasPlaceholderImage,
    'classList' => array_merge($classList ?? []),
    'attributeList' => $post->attributeList ?? []
])
    @includeWhen(
        !empty($post->callToActionItems['floating']['icon']),
        'partials.floating'
    )

    @slot('aboveContent')
        @includeWhen(!empty($post->readingTime), 'partials.read-time')
        @includeWhen($post->commentCount !== false, 'partials.comment-count')
    @endslot
@endsegment
