@collapsiblesearch([
    'button' => [
        'text' => $lang->search,
        'icon' => 'search',
        'style' => $menuItem->getButtonStyle(),
        'color' => $menuItem->getButtonColor(),
        'size' => $menuItem->getButtonSize(),
        'ariaLabel' => $lang->search,
        'reversePositions' => true,
    ],
    'placeholder' => $lang->searchQuestion,
    'inputLabel' => $lang->searchQuestion,
    'action' => $homeUrl,
    'method' => 'get',
    'closeLabel' => $lang->searchClose,
    'classList' => $menuItem->getCssClasses(),
])
@endcollapsiblesearch