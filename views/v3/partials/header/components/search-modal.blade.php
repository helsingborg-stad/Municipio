@element([
    'classList' => $menuItem->getCssClasses()
])
    @button([
        'text' => $lang->search,
        'icon' => 'search',
        'color' => $menuItem->getButtonColor(),
        'style' => $menuItem->getButtonStyle(),
        'size' => $menuItem->getButtonSize(),
        'reversePositions' => true,
        'classList' => [
            's-header-button'
        ],
        'attributeList' => [
            'data-open' => 'm-search-modal__trigger',
        ],
    ])
    @endbutton
@endelement