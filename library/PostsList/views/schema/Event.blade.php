@segment([
    'layout'            => 'card',
    'image'             => $post->getImage(),
    'link'              => $post->getPermalink(),
    'containerAware'    => true
])
    @slot('floating')
        @datebadge([ 'date' => $getSchemaEventDateBadgeDate($post), 'size' => 'sm', ]) @enddatebadge
    @endslot
    @slot('aboveContent')
        @typography(['variant' => 'h5', 'classList' => ['u-margin__bottom--0', 'u-color__text--primary']]){!! $getSchemaEventPlaceName($post) !!}@endtypography
        @typography(['element' => 'h2', 'variant' => 'h4', 'classList' => ['u-margin__bottom--2', 'u-margin__top--0']]){!!$post->getTitle()!!}@endtypography
        @typography(['variant' => 'date', 'classList' => ['u-margin__top--0']]){{ $getSchemaEventDate($post) }}@endtypography
        @if(!empty($getSchemaEventPriceRange($post)))
            @element([
                'classList' => [ 'u-margin__top--1', 'u-padding__x--1', 'u-border--1', 'u-color__text--primary' ],
                'attributeList' => [ 'style' => 'border-radius: 8px; display: inline-block;' ]
            ])
                {{ $getSchemaEventPriceRange($post) }}
            @endelement
        @endif
    @endslot
@endsegment