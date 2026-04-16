@if((!empty($bookingLink) || !empty($priceListItems)) && !$eventIsInThePast)
    @card([
        'heading' => $lang->bookingTitle
    ])
        @slot('aboveContent')

            @if(!empty($priceListItems))
                @listing([
                    'list' => array_map(function($priceListItem) {
                        return [ 'label' => $priceListItem->getName() . ': ' . $priceListItem->getPrice() ];
                    }, $priceListItems)
                ])
                @endlisting
            @endif

            @if(!empty($bookingLink))
                @button([
                    'href' => $bookingLink,
                    'color' => 'primary',
                    'style' => 'filled',
                    'size' => 'md',
                    'icon' => 'open_in_new',
                    'fullWidth' => false,
                    'text' => $lang->bookingButton,
                    'classList' => [
                        'u-margin__top--2'
                    ],
                    'attributeList' => [
                        'style' => 'justify-self: start;',
                    ],
                    'target' => '_blank'
                ])
                @endbutton
                @typography([
                    'element' => 'span',
                    'variant' => 'meta'
                ])
                    {!! $lang->bookingDisclaimer !!}
                @endtypography
            @endif
        @endslot
    @endcard
@endif