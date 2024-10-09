@if (!empty($mobileMenu['items']) || !empty($menuItems))
    @nav([
        'id' => 'menu-mobile',
        'items' => $menuItems ?? $mobileMenu['items'],
        'direction' => 'vertical',
        'includeToggle' => true,
        'classList' => $classList,
        'depth' => $depth ?? 1,
        'expandLabel' => $lang->expand
    ])
    @endnav
@else
    {{-- No menu items found --}}
@endif
