@if (!empty($mobileMenu['items']))
    @nav([
        'id' => 'menu-mobile',
        'title' => $mobileMenu['title'],
        'items' => $mobileMenu['items'],
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
