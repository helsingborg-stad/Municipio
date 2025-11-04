@block([
    'link' => $post->getPermalink(),
    'heading' => $post->getTitle(),
    'ratio' => $archiveProps->format == 'tall' ? '12:16' : '1:1',
    'meta' => $getTags($post),
    'secondaryMeta' => $getReadingTime($post),
    'image' => $post->getImage() ?? null ? [
        'src' => $post->getImage(),
        'backgroundColor' => 'secondary'
    ] : [
        'src' => $archiveProps->format == 'tall' ? $post->images['thumbnail3:4']['src'] ?? false : $post->images['thumbnail16:9']['src'] ?? false,
        'alt' => $post->images['thumbnail16:9']['alt'] ?? '' ? $post->images['thumbnail16:9']['alt'] ?? '' : $post->getTitle(),
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
