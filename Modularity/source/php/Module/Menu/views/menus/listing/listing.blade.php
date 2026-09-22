@element([
    'classList' => array_merge([
        'mod-menu__wrapper',
        $wrapped ? 'u-shadow--1' : '',
        'o-layout-grid',
        'o-layout-grid--cq'
    ], $columnClasses)
])
    @foreach ($menu['items'] as $index => $menuItem)
        @include('menus.listing.components.menuItem')
    @endforeach
@endelement