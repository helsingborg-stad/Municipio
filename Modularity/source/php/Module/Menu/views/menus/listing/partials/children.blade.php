@if(!empty($menuItem['children']))
    @element([
        'componentElement' => 'div',
        'classList' => [
            'mod-menu__children',
            'u-margin__top--1',
            'o-layout-grid',
            'o-layout-grid--cols-1',
            'o-layout-grid--gap-2'
        ]
    ])
        @foreach(array_slice($menuItem['children'], 0, 3) as $child)
            @include('menus.listing.partials.child')
        @endforeach
    @endelement
    {{-- @if(count($menuItem['children']) > 3)
        @element([
            'classList' => [
                'mod-menu__expandable-wrapper'
            ]
        ])
            @element([
                'componentElement' => 'div',
                'classList' => [
                    'mod-menu__children',
                    'mod-menu__children--hidden',
                    'o-layout-grid',
                    'o-layout-grid--cols-1',
                    'o-layout-grid--gap-2'
                ]
            ])
                @foreach(array_slice($menuItem['children'], 3) as $child)
                    @include('menus.listing.partials.child')
                @endforeach
            @endelement
        @endelement
    @endif --}}
@endif
