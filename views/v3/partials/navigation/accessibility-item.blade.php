<li role="menuitem">
    @button([
        'text' => $item['text'] ?? false,
        'style' => 'outlined',
        'color' => 'primary',
        'href' => $item['href'] ?? false,
        'icon' => $item['icon'] ?? null,
        'size' => 'sm',
        'attributeList' => array_merge($item['attributeList'] ?? [], [
            'onClick' => $item['script'] ?? '',
            'aria-label' => $item['label'] ?? '',
        ])
    ])
    @endbutton
</li>