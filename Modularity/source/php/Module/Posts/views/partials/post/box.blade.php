@box([
    'heading' => $post->postTitle,
    'content' => $post->excerptShort,
    'link' => $post->permalink,
    'meta' => $post->termsUnlinked,
    'date' => $showDate ? [
        'timestamp' => $post->getArchiveDateTimestamp(),
        'format'    => $post->getArchiveDateFormat(),
    ] : null,
    'dateBadge' => \Municipio\Helper\DateFormat::getUnresolvedDateFormat($post) == 'date-badge',
    'ratio' => $ratio,
    'image' => $post->image,
    'attributeList' => $post->attributeList ?? []
])
    @slot('metaArea')
        @includeWhen(!empty($post->readingTime), 'partials.read-time')
        @includeWhen($post->commentCount !== false, 'partials.comment-count')
    @endslot
@endbox
