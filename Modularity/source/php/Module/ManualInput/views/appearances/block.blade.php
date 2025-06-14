@block([
    'heading'   => $input['title'],
    'content'   => $input['content'],
    'ratio'     => $input['isHighlighted'] ? '16:9' : $ratio,
    'image'     => $input['image'],
    'classList' => array_merge($input['classList'] ?? [], [$input['columnSize'], 'u-height--100']),
    'context'   => $context,
    'link'      => $input['link'],
    'attributeList' => $input['attributeList'] ?? [],
    'icon'      => $input['icon'] ? [
        'icon' => $input['icon'],
        'size' => 'md',
        'color' => 'white'
    ] : null
])
@endblock