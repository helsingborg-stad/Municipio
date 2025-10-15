@if ($posts)
    @element(['classList' => ['o-grid']])
        @foreach ($posts as $post)
            @element(['classList' => explode(' ', $gridColumnClass)])
                @segment([
                    'layout'            => 'card',
                    'image'             => $post->getImage(),
                    'link'              => $post->getPermalink(),
                    'containerAware'    => true
                ])
                    @slot('floating')
                        @datebadge([ 'date' => $getDatebadgeDate($post), 'size' => 'sm', ]) @enddatebadge
                    @endslot
                    @slot('aboveContent')
                        @typography(['variant' => 'h5', 'classList' => ['u-margin__bottom--0', 'u-color__text--primary']]){!! $getEventPlaceName($post) !!}@endtypography
                        @typography(['element' => 'h3', 'variant' => 'h4', 'classList' => ['u-margin__bottom--2', 'u-margin__top--0']]){!!$post->getTitle()!!}@endtypography
                        @typography(['variant' => 'date', 'classList' => ['u-margin__top--0']]){{ $getEventDate($post) }}@endtypography
                        @if(!empty($getEventPriceRange($post)))
                            @element([
                                'classList' => [ 'u-margin__top--1', 'u-padding__x--1', 'u-border--1', 'u-color__text--primary' ],
                                'attributeList' => [ 'style' => 'border-radius: 8px; display: inline-block;' ]
                            ])
                                {{ $getEventPriceRange($post) }}
                            @endelement
                        @endif
                    @endslot
                @endsegment
            @endelement
        @endforeach
    @endelement
@endif
