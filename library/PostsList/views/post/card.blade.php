@card([
    'link' => $post->getPermalink(),
    'image' => $post->getImage(),
    'heading' => $post->getTitle(),
    'content' => $getExcerpt($post, 20),
    'tags' => $getTags($post),
    'meta' => $getReadingTime($post),
    'date' => [
        'timestamp' => $getDateTimestamp($post),
        'format'    => $getDateFormat(),
    ],
    'dateBadge' => $showDateBadge(),
    'context' => ['archive', 'archive.list', 'archive.list.card'],
    'containerAware' => true,
    'hasPlaceholder' => $appearanceConfig->shouldDisplayPlaceholderImage() && !$post->getImage(),
])
@endcard