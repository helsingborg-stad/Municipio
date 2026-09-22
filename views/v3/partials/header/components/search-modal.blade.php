@button([
    'text' => $lang->search,
    'color' => $buttonAppearance['color'],
    'icon' => 'search',
    'style' => $buttonAppearance['style'],
    'size' => $buttonAppearance['size'],
    'reversePositions' => true,
    'classList' => [
        's-header-button'
    ],
    'attributeList' => [
        'data-open' => 'm-search-modal__trigger',
],
])
@endbutton