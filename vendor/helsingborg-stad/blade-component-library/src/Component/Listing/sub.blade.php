{{--- Sub list (Recursive) ---}}
@if(isset($item['children']))
    @listing([
        'list' => $item['children'],
        'elementType' => isset($item['childrenElementType']) ? $item['childrenElementType'] : $elementType,
        'classList' => [$baseClass, $baseClass . '__sub']
    ])
    @endlisting
@endif