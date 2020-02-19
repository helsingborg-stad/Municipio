@icon([
    'icon' => 'search',
    'size' => 'xl',
    'color' => 'Primary'
])
@endicon

@field([
    'type' => 'text',
    'attributeList' => [
        'type' => 'search',
        'name' => 's',
        'required' => false,
        'id' => 'filter-keyword',
        'value' => $searchQuery
    ],
    'label' => _e('Search', 'municipio'),
    'classList' => ['text-sm' 'sr-only']
])
@endfield
