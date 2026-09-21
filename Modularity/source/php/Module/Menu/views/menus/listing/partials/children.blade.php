@if(!empty($menuItem['children']))
    @element([
        'componentElement' => 'div',
        'classList' => [
            'mod-menu__children',
            'u-margin__top--2',
            'o-layout-grid',
            'o-layout-grid--cols-1',
            'o-layout-grid--gap-2'
        ]
    ])
        @foreach($menuItem['children'] as $child)
            @include('menus.listing.partials.child')
        @endforeach
    @endelement
@endif
