@element([
    'attributeList' => $input['attributeList'] ?? [],
    'classList'     => array_merge($input['classList'] ?? [], [$input['columnSize']])
])
    @card([
        'link'              => $input['showLinkAsButton'] ? '' : ($input['link'] ?? ''),
        'linkText'          => (!$input['showLinkAsButton'] && isset($input['linkText'])) ? $input['linkText'] : '',
        'eyebrow'           => !empty($input['eyebrow']) ? $input['eyebrow'] : '',
        'heading'           => $input['title'],
        'context'           => $context,
        'content'           => $input['content'],
        'image'             => $input['image'],
        'classList'         => array_merge($input['classList'] ?? [], ['u-height--100']),
        'hasPlaceholder'    => !empty($anyItemHasImage),
        'headingAboveImage' => isset($titleAboveImage) ? $titleAboveImage : false,
        'icon'              => $input['icon'] ? [
            'icon' => $input['icon'],
            'size' => 'md',
            'color' => 'black'
        ] : false,
        'buttons'           => $input['showLinkAsButton'] && !empty($input['link']) ? [[
            'text' => $input['linkText'] ?: ($input['defaultLinkText'] ?? __('Read more', 'municipio')),
            'href' => $input['link'],
            'color' => $input['buttonColor'] ?? 'primary',
            'style' => 'filled',
        ]] : [],
    ])
    @endcard
@endelement
