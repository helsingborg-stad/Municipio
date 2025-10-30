@card([
    'link' => $post->getPermalink(),
    'image' => $post->getImage(),
    'heading' => $post->getTitle(),
    'content' => \Municipio\Helper\Sanitize::sanitizeATags($post->getExcerpt()),
    'tags' => $post->termsUnlinked,
    'meta' => $config->shouldDisplayReadingTime() ? \Municipio\Helper\ReadingTime::getReadingTimeFromPostObject($post) : '',
    'date' => [
        'timestamp' => $post->getArchiveDateTimestamp(),
        'format'    => $post->getArchiveDateFormat(),
    ],
    'dateBadge' => \Municipio\Helper\DateFormat::getUnresolvedDateFormat($post) == 'date-badge',
    'context' => ['archive', 'archive.list', 'archive.list.card'],
    'containerAware' => true,
    'hasPlaceholder' => $config->shouldDisplayPlaceholderImage() && !$post->getImage(),
])
@endcard