<li role="menuitem">
    @button([
        'text' => $item['text'] ?? false,
        'style' => $item['style'] ?? 'outlined',
        'color' => $item['color'] ?? 'primary',
        'href' => $item['href'] ?? false,
        'icon' => $item['icon'] ?? null,
        'size' => $item['iconSize'] ?? 'sm',
        'attributeList' => array_merge($item['attributeList'] ?? [], [
            'onClick' => $item['script'] ?? '',
            'aria-label' => $item['label'] ?? '',
        ])
    ])
    @endbutton
</li>