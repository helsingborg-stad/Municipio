{{--- Sub list (Recursive) ---}}
@if(isset($item['children']) && is_array($item['children']))
    @menu([
        'wrapper' => false,
        'items' => $item['children'],
        'classList' => [$baseClass, $baseClass . '__child']
    ])
    @endmenu 
@endif