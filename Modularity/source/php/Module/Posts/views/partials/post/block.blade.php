@block([
    'heading' => $post->postTitle,
    'content' => $post->excerptShort,
    'ratio' => $ratio,
    'meta' => $post->termsUnlinked,
    'secondaryMeta' => $post->readingTime,
    'date'          => $showDate ? [
        'timestamp' => $post->getArchiveDateTimestamp(),
        'format'    => $post->getArchiveDateFormat(),
    ] : null,
    'dateBadge' => \Municipio\Helper\DateFormat::getUnresolvedDateFormat($post) == 'date-badge',
    'image' => $post->image,
    'classList' => ['t-posts-block', ' u-height--100'],
    'context' => ['module.posts.block'],
    'link' => $post->permalink,
    'icon' => $post->getIcon() ? [
        'icon' => $post->getIcon()->getIcon(),
        'color' => 'white',
    ] : null,
    'iconBackgroundColor' => $post->getIcon() ? $post->getIcon()->getCustomColor() : null,
    'attributeList' => $post->attributeList ?? []
])
    @includeWhen(
        !empty($post->callToActionItems['floating']['icon']), 
        'partials.floating'
    )
    @slot('metaArea')
        @includeWhen($post->commentCount !== false, 'partials.comment-count')
    @endslot
@endblock