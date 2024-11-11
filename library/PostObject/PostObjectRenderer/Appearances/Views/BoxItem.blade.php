@box([
    'heading' => $postObject->postTitle,
    'content' => $postObject->excerptShort,
    'link' => $postObject->permalink,
    'meta' => $postObject->termsUnlinked,
    'secondaryMeta' => $postObject->readingTime,
    'date' => $postObject->postDateFormatted,
    'ratio' => $config['ratio'],
    'image' => $postObject->imageContract ?? $postObject->image
])
@endbox
