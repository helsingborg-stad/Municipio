@segment([
    'layout' => 'card',
    'title' => $postObject->getTitle(),
    'context' => ['module.posts.segment'],
    'meta' => $postObject->readingTime,
    'tags' => $postObject->termsUnlinked,
    'image' => $postObject->image,
    'date' => $postObject->postDateFormatted,
    'content' => $postObject->excerptShort,
    'buttons' => [['text' => $lang->readMore, 'href' => $postObject->getPermalink(), 'color' => 'primary']],
    'containerAware' => true,
    'reverseColumns' => $config['reverseColumns'],
    'icon' => $postObject->termIcon ?? null,
    'hasPlaceholder' => $postObject->hasPlaceholderImage,
    'attributeList' => $postObject->attributeList ?? [],
    'classList' => $classList ?? [],
])
@includeWhen(!empty($postObject->callToActionItems['floating']), 'Partials.Floating')
@endsegment
