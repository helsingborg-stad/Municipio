@segment([
    'layout' => 'card',
    'title' => $input['title'],
    'context' => $context,
    'image' => $input['image'],
    'content' => $input['content'] ?? false,
    'buttons' => [['text' => $input['linkText'] ?? $input['defaultLinkText'], 'href' => $input['link'], 'target' => $linkTarget ?: '_self', 'color' => 'primary']],
    'containerAware' => true,
    'reverseColumns' => !empty($imagePosition),
    'classList' => array_merge($input['classList'] ?? [], [$input['columnSize']]),
    'hasPlaceholder' => $anyItemHasImage,
    'attributeList' => $input['attributeList'] ?? [],
    'icon' => [
        'icon' => $input['icon'],
        'size' => 'md',
        'color' => 'black'
    ]
])
@endsegment
