@extends('templates.single', ['showSidebars' => false, 'centerContent' => true])

@section('above')
    @hero([
        "image" => $post->imageContract,
        "size" => "normal",
        "classList" => ["u-margin__bottom--4"],
    ])
    @endhero
@stop

@section('article.content')

    @section('article.title.before')
        @if($eventIsInThePast)
            @notice([
                'type' => 'warning',
                'message' => [
                    'text' => $lang->expiredEventNotice,
                ],
                'icon' => [
                    'icon' => 'schedule'
                ]
            ])@endnotice
        @endif
    @stop

    @section('article.title.after')

        @collection(['classList' => ['u-display--flex']])
            @if(!empty($currentOccasion))
                @collection__item([ 'icon' => 'event', 'classList' => ['u-width--50'] ])
                    @typography(['element' => 'h4', 'variant' => 'h2'])
                        {!!$lang->occasionsTitle!!}
                    @endtypography
                    @typography([])
                        {!!$currentOccasion->getStartDate()!!}
                    @endtypography
                @endcollection__item
            @endif
            @collection__item([ 'icon' => 'location_on', 'classList' => ['u-width--50'] ])
                @typography(['element' => 'h4', 'variant' => 'h2'])
                    {!!$lang->placeTitle!!}
                @endtypography
                @typography([])
                    {!!$place['address']!!}
                    @if(!empty($place['url']))
                        @link(['href' => $place['url']])
                            {{$lang->directionsLabel}}
                        @endlink
                    @endif
                @endtypography
            @endcollection__item
        @endcollection

    @stop
    
    @if(!empty($description))
        {!!$description!!}
    @endif

    @if(!$eventIsInThePast)
        @button([
            'href' => $icsUrl,
            'color' => 'primary',
            'style' => 'filled',
            'size' => 'md',
            'fullWidth' => true,
            'text' => $lang->addToCalendar,
            'icon' => 'calendar_add_on',
            'classList' => ['u-margin__top--4']
        ])
        @endbutton
    @endif

    @iconSection()
        
        @if(!empty($priceListItems))
            @iconSection__item(['icon' => ['icon' => 'payments', 'size' => 'md']])
                @include('partials.post.schema.event.icon-section-header', ['header' => $lang->priceTitle])
                @foreach ($priceListItems as $priceListItem)
                    @element([])
                        @element(['componentElement' => 'strong']){!!$priceListItem->getName() !!}@endelement
                        @element(['componentElement' => 'span']){!! $priceListItem->getPrice() !!}@endelement
                    @endelement
                @endforeach
            @endiconSection__item
        @endif

        @if(!empty($organizers))
            
            @iconSection__item(['icon' => ['icon' => 'group', 'size' => 'md']])
                @include('partials.post.schema.event.icon-section-header', ['header' => $lang->organizersTitle])
                @foreach($organizers as $organizer)
                    @if(!empty($organizer['name'])) 
                        
                        @element([])
                            @typography(['element' => 'h3', 'variant' => 'h4', 'classList' => ['u-margin__bottom--2']]){!!$organizer['name']!!}@endtypography

                            @if(!empty($organizer['url']))
                                @element(['classList' => ['u-width--100', 'u-truncate', 'u-margin__bottom--1']])
                                    @icon(['icon' => 'language'])@endicon @link(['href' => $organizer['url']]) {!!$organizer['url']!!} @endlink
                                @endelement
                            @endif
                            
                            @if(!empty($organizer['email']))
                                @element(['classList' => ['u-width--100', 'u-truncate', 'u-margin__bottom--1']])
                                    @icon(['icon' => 'email'])@endicon @link(['href' => 'mailto:'.$organizer['email']]) {!!$organizer['email']!!} @endlink
                                @endelement
                            @endif
                            @if(!empty($organizer['telephone']))
                                @element(['classList' => ['u-width--100', 'u-truncate']])
                                    @icon(['icon' => 'phone'])@endicon @link(['href' => 'tel:'.$organizer['telephone']]) {!!$organizer['telephone']!!} @endlink
                                @endelement
                            @endif
                        @endelement

                    @endif
                @endforeach
            @endiconSection__item
        @endif  

        @if(!empty($occasions) && count($occasions) > 1)
            @iconSection__item(['icon' => ['icon' => 'calendar_month', 'size' => 'md']])
                @include('partials.post.schema.event.icon-section-header', ['header' => $lang->moreOccasions])
                @collection(['compact' => true])
                    @foreach($occasions as $occasion)
                        @collection__item([
                            'icon' => $occasion->isCurrent() ? 'arrow_forward' : 'event',
                            'link' => $occasion->getUrl()
                        ])
                            @typography(['classList' => [$occasion->isCurrent() ? 'u-bold' : '']])
                                {!! $occasion->getStartDate() !!}
                            @endtypography
                        @endcollection__item
                    @endforeach
                @endcollection
            @endiconSection__item
        @endif

        @if(!empty($bookingLink) && !$eventIsInThePast)
            @iconSection__item(['icon' => ['icon' => 'local_activity', 'size' => 'md']])
                @include('partials.post.schema.event.icon-section-header', ['header' => $lang->bookingTitle])
                @button([
                    'href' => $bookingLink,
                    'color' => 'primary',
                    'style' => 'filled',
                    'size' => 'md',
                    'icon' => 'arrow_forward',
                    'fullWidth' => true,
                    'text' => $lang->bookingButton,
                ])
                @endbutton
                @element(['componentElement' => 'small']){!!$lang->bookingDisclaimer!!}@endelement
            @endiconSection__item
        @endif

        @if(!empty($accessibilityFeatures))

            @iconSection__item(['icon' => ['icon' => 'accessibility', 'size' => 'md']])
                @include('partials.post.schema.event.icon-section-header', ['header' => $lang->accessibilityTitle])
                @element(['componentElement' => 'ul'])
                    @foreach($accessibilityFeatures as $feature)
                        @element(['componentElement' => 'li']){!! $feature !!}@endelement
                    @endforeach
                @endelement
            @endiconSection__item
        @endif

    @endiconSection
    
@stop