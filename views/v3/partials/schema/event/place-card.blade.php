 @card([
    'heading' => $lang->placeTitle,
    'content' => $place['address']
])
    @if(!empty($place['url']))
        @slot('belowContent')
                @link(['href' => $place['url']])
                    {{$lang->directionsLabel}}
                @endlink
        @endslot
    @endif
@endcard
