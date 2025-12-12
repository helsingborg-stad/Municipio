@segment([
    'layout'            => 'card',
    'image'             => $post->getImage(),
    'link'              => $post->getPermalink(),
    'containerAware'    => true
])
    @if(!empty($getSchemaEventDateBadgeDate($post)))
        @slot('floating')
            @datebadge([ 'date' => $getSchemaEventDateBadgeDate($post), 'size' => 'sm']) @enddatebadge
        @endslot
    @endif
    @slot('aboveContent')
        @typography(['element' => 'h2', 'variant' => 'h4', 'classList' => ['u-margin__top--0']]){!!$post->getTitle()!!}@endtypography
        
        @element(['classList' => ['u-margin__bottom--0', 'u-margin__top--2', 'u-display--flex', 'u-flex-direction--column', 'o-layout-grid--gap-1']])
            @typography(['variant' => 'meta', 'classList' => ['u-margin__top--0', 'u-margin__bottom--0', 'u-display--flex', 'u-align-items--center', 'o-layout-grid--gap-1']])
                @icon(['icon' => 'location_on', 'size' => 'sm'])@endicon
                {!! $getSchemaEventPlaceName($post) !!}
            @endtypography
            @typography(['variant' => 'meta', 'classList' => ['u-margin__top--0', 'u-margin__bottom--0', 'u-display--flex', 'u-align-items--center', 'o-layout-grid--gap-1']])
                @icon(['icon' => 'event', 'size' => 'sm'])@endicon
                {{ $getSchemaEventDate($post) }} 
            @endtypography
        @endelement

        @if(!empty($getSchemaEventHasMoreOccasions($post)))
            @element([
                'classList' => [ 'u-margin__top--2', 'u-padding__x--1', 'u-border--1', 'u-color__text--primary' ],
                'attributeList' => [ 'style' => 'border-radius: 8px; display: inline-block;' ]
            ])
                @typography(['element' => 'span', 'variant' => 'meta'])
                    {{ $getEventMoreOccasionsLabel() }}
                @endelement
            @endelement
        @endif
    @endslot
@endsegment