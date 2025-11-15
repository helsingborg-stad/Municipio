@block([
    'link' => $post->getPermalink(),
    'heading' => $post->getTitle(),
    'ratio' => $appearanceConfig->getImageRatio(),
    'meta' => $getTags($post),
    'secondaryMeta' => $getReadingTime($post),
    'image' => [
        'src' => $post->getImage() ?? $appearanceConfig->getPlaceholderImageUrl() ?? null,
        'backgroundColor' => 'secondary'
    ],
    'date' => [
        'timestamp' => $post->getArchiveDateTimestamp(),
        'format'    => $post->getArchiveDateFormat(),
    ],
    'dateBadge' => $showDateBadge(),
    'classList' => ['t-archive-block'],
    'context' => ['archive', 'archive.list', 'archive.list.block'],
])
@endblock
