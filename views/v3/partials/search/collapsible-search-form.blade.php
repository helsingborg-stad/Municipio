@collapsiblesearch([
    'button' => [
        'text' => $lang->search,
        'icon' => 'search',
        'style' => $buttonAppearance['style'] ?? 'basic',
        'color' => $buttonAppearance['color'] ?? 'inherit',
        'size' => $buttonAppearance['size'] ?? 'md',
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