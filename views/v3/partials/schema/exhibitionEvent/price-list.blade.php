@collection()
    @collection__item([])
        @typography(['element' => 'h4', 'variant' => 'h4'])
            {!! $lang->entranceLabel !!}
        @endtypography
        @foreach ($priceListItems as $priceListItem)
            @element([])
                @element(['componentElement' => 'span']){!!$priceListItem->getName() !!}: @endelement
                @element(['componentElement' => 'span', 'classList' => ['u-float--right']]){!! $priceListItem->getPrice() !!}@endelement
            @endelement
        @endforeach
    @endcollection__item
@endcollection