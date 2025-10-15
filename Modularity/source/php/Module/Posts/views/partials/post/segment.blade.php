@segment([
    'layout' => 'card',
    'title' => $post->postTitle,
    'context' => ['module.posts.segment'],
    'tags' => $post->termsUnlinked,
    'image' => $post->image,
    'date'  => $showDate ? [
        'timestamp' => $post->getArchiveDateTimestamp(),
        'format'    => $post->getArchiveDateFormat(),
    ] : null,
    'content' => $post->excerptShort,
    'buttons' => [['text' => $lang['readMore'], 'href' => $post->permalink, 'color' => 'primary']],
    'containerAware' => true,
    'reverseColumns' => $imagePosition,
    'icon' => $post->getIcon() ? [
        'icon' => $post->getIcon()->getIcon(),
        'color' => 'white',
    ] : null,
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