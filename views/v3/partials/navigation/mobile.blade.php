@if (!empty($mobileMenu))
{{-- @if (!empty($mobileMenu['items'])) --}}
    @nav([
        'id' => 'menu-mobile',
        'items' => $mobileMenu,
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
