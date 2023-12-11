<li role="menuitem">
    @link([
        'href' => $item['href'] ?? null,
        'attributeList' => array_merge($item['attributeList'] ?? [], [
            'onClick' => $item['script'] ?? '',
            'aria-label' => $item['label'] ?? '',
        ]),
    ])
    @icon([
        'icon' => $item['icon'],
        'size' => $item['size'] ?? 'md',
        'filled' => $item['filled'] ?? true,
        'classList' => $item['classList'] ?? []
    ])
    @endicon
    {{ $item['text'] }}
    @endlink
</li>