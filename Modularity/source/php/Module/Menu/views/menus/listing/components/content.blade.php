@group([
    'fluidGrid' => $columns,
    'flexWrap' => 'wrap',
    'display' => 'grid',
    'classList' => array_merge([
        'mod-menu__container',
    ], $gridClasses ?? [])
])
    @foreach ($menu['items'] as $index => $menuItem)
        <div 
            class="mod-menu__item {{implode(' ', $menuItem['classList'] ?? [])}}@if (!$mobileCollapse) is-expanded @endif" 
            @if ($mobileCollapse)
            data-js-toggle-item="mod-menu-item-{{$ID}}-{{$index}}" data-js-toggle-class="is-expanded"
            @endif
            >
            @group([
                'display' => 'grid',
                'classList' => [
                    'mod-menu__grid'
                ],
                'attributeList' => [
                    'style' => 'grid-template-columns: auto 1fr;'
                ],
                'gap' => 1
            ])
                @includeWhen(!empty($menuItem['icon']['icon']), 'menus.listing.partials.icon')
                @includeWhen(!empty($menuItem['label']), 'menus.listing.partials.parent')
                @includeWhen(!empty($menuItem['description']), 'menus.listing.partials.description')
                @includeWhen(!empty($menuItem['children']), 'menus.listing.partials.children')
            @endgroup
            @includeWhen(!empty($menuItem['children'] && $mobileCollapse), 'menus.listing.partials.expand')
        </div>
    @endforeach
    @for ($i = 0; $i < ($fakeItems ?? 0); $i++)
        <div class="mod-menu__item mod-menu__item--fake"></div>
    @endfor
@endgroup