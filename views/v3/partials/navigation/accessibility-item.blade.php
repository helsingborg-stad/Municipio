<li role="menuitem">
    @link([
        'href' => $item['href'] ?? null,
        'attributeList' => [
            'onClick' => $item['script'] ?? '',
            'aria-label' => $item['label'] ?? '',
        ],
    ])
    @icon([
        'icon' => $item['icon'],
        'size' => $item['size'] ?? 'md',
        'filled' => $item['filled'] ?? true,
        'attributeList' => $item['attributeList'] ?? [],
        'classList' => $item['classList'] ?? []
    ])
    @endicon
    {{ $item['text'] }}
    @endlink
</li>