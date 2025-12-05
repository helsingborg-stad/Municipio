@if(!empty($bookingLink) && !$eventIsInThePast)
    @card([
        'heading' => $lang->bookingTitle
    ])
        @slot('aboveContent')
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
        @endslot
    @endcard
@endif