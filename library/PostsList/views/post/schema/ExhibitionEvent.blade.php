@card([
    'link' => $post->getPermalink(),
    'image' => $post->getImage(),
    'heading' => $post->getTitle(),
    'content' => $getExcerpt($post, 20),
    'tags' => $getTags($post),
    'meta' => $getSchemaExhibitionOccasionText($post),
    'context' => ['archive', 'archive.list', 'archive.list.card'],
    'containerAware' => true,
    'hasPlaceholder' => $shouldDisplayPlaceholderImage($post),
    'attributeList' => array_merge(['data-js-posts-list-item' => true], $getViewTransitionAttributes($post->getId())),
])
@endcard
