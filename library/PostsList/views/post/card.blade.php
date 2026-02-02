@card([
    'link' => $post->getPermalink(),
    'image' => $post->getImage(),
    'heading' => $post->getTitle(),
    'content' => $getExcerpt($post, 20),
    'tags' => $getTags($post),
    'meta' => $getReadingTime($post),
    'date' => $getDateFormat() ? [
        'timestamp' => $getDateTimestamp($post),
        'format'    => $getDateFormat(),
    ] : null,
    'dateBadge' => $showDateBadge(),
    'classList' => ['u-height--100'],
    'context' => ['archive', 'archive.list', 'archive.list.card'],
    'containerAware' => true,
    'hasPlaceholder' => $shouldDisplayPlaceholderImage($post),
    'attributeList' => ['data-js-posts-list-item' => true],
])
@endcard