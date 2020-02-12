@foreach ($icons as $indexKey => $icon)
    @button([
        'icon' => $icon["name"],
        'attributeList' => [
            'tabindex' => $indexKey
        ],
        'classList' => $icon["classList"],
        'type' => 'basic',
        'color' => 'primary'
    ])
    @endbutton
@endforeach