@button([
    'text' => $lang->search,
    'color' => $buttonAppearance['color'] ?? 'inherit',
    'icon' => 'search',
    'style' => $buttonAppearance['style'] ?? 'basic',
    'size' => $buttonAppearance['size'] ?? 'md',
    'reversePositions' => true,
    'classList' => [
        's-header-button'
    ],
    'attributeList' => [
        'data-open' => 'm-search-modal__trigger',
],
])
@endbutton