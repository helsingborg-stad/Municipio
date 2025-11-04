@card([
    'link' => $post->getPermalink(),
    'image' => $post->getImage(),
    'heading' => $post->getTitle(),
    'content' => $getExcerptWithoutLinks($post),
    'tags' => $getTags($post),
    'meta' => $getReadingTime($post),
    'date' => [
        'timestamp' => $post->getArchiveDateTimestamp(),
        'format'    => $post->getArchiveDateFormat(),
    ],
    'dateBadge' => $showDateBadge(),
    'context' => ['archive', 'archive.list', 'archive.list.card'],
    'containerAware' => true,
    'hasPlaceholder' => $config->shouldDisplayPlaceholderImage() && !$post->getImage(),
])
@endcard