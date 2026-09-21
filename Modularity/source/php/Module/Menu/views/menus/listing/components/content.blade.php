@element([
    'classList' => array_merge([
        'o-layout-grid',
        'o-layout-grid--cq'
    ], $columnClasses)
])
    @foreach ($menu['items'] as $index => $menuItem)
        @include('menus.listing.components.menuItem')
    @endforeach
@endelement
