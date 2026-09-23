@collapsiblesearch([
    'button' => [
        'text' => $lang->search,
        'icon' => 'search',
        'style' => $buttonAppearance['style'],
        'color' => $buttonAppearance['color'],
        'size' => $buttonAppearance['size'],
        'ariaLabel' => $lang->search,
        'reversePositions' => true,
    ],
    'placeholder' => $lang->searchQuestion,
    'inputLabel' => $lang->searchQuestion,
    'action' => $homeUrl,
    'method' => 'get',
    'closeLabel' => $lang->searchClose,
    'classList' => $classList ?? [],
])
@endcollapsiblesearch