@newsItem([
    'heading'             => $input['title'],
    'content'             => $input['content'],
    'image'               => $input['image'] ?: null,
    'link'                => $input['link'],
    'context'             => $context,
    'hasPlaceholderImage' => $anyItemHasImage,
    'classList'           => array_merge($input['classList'] ?? [], [$input['columnSize']]),
    'attributeList'       => $input['attributeList'] ?? [],
])
@endnewsItem
