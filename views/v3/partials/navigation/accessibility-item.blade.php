<li role="menuitem">
    @button([
        'text' => $item['text'],
        'style' => 'outlined',
        'color' => 'primary',
        'href' => $item['href'] ?? false,
        'icon' => $item['icon'],
        'size' => 'sm',
        'attributeList' => array_merge($item['attributeList'] ?? [], [
            'onClick' => $item['script'] ?? '',
            'aria-label' => $item['label'] ?? '',
        ])
    ])
    @endbutton
</li>