@block([
    'link' => $post->getPermalink(),
    'heading' => $post->getTitle(),
    'ratio' => $appearanceConfig->getImageRatio(),
    'meta' => $getTags($post),
    'secondaryMeta' => $getReadingTime($post),
    'image' => $post->getImage() ?? $appearanceConfig->getPlaceholderImageUrl() ?? null,
    'date' => $getDateFormat() ? [
        'timestamp' => $getDateTimestamp($post),
        'format'    => $getDateFormat(),
    ] : null,
    'dateBadge' => $showDateBadge(),
    'classList' => ['t-archive-block'],
    'context' => ['archive', 'archive.list', 'archive.list.block'],
])
@endblock
