@extends('templates.single')

@section('hero-top-sidebar')
    @hero([
        "image" => $post->imageContract,
        "size" => "small"
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
                    'name' => 'schedule'
                ]
            ])@endnotice
        @endif
    @stop
    
    {!!$post->schemaObject['description']!!}

    @element()

        @if(!empty($placeUrl) && !empty($placeName) && !empty($placeAddress))
            @iconSection(['icon' => ['icon' => 'location_on', 'size' => 'md']])
                @include('partials.post.schema.event.icon-section-header', ['header' => $lang->placeTitle])
                @element(['componentElement' => 'strong']){!!$placeName!!}@endelement
                @element(['componentElement' => 'address'])
                    @link(['href' => $placeUrl])
                        {!!$placeAddress!!}
                    @endlink
                @endelement
            @endiconSection

        @endif

        @if(!empty($priceListItems))
            @iconSection(['icon' => ['icon' => 'payments', 'size' => 'md']])
                @include('partials.post.schema.event.icon-section-header', ['header' => $lang->priceTitle])
                @foreach ($priceListItems as $priceListItem)
                    @element([])
                        @element(['componentElement' => 'strong']){!!$priceListItem->getName() !!}@endelement
                        @element(['componentElement' => 'span']){!! $priceListItem->getPrice() !!}@endelement
                    @endelement
                @endforeach
            @endiconSection
        @endif

        @if(!empty($organizers))
            @iconSection(['icon' => ['icon' => 'group', 'size' => 'md']])
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
            @endiconSection
        @endif
    @endelement
    
@stop

@section('sidebar.right-sidebar.before')

    @iconSection(['icon' => ['icon' => 'event', 'size' => 'md']])
        @include('partials.post.schema.event.icon-section-header', ['header' => $lang->occassionsTitle])
        @typography(){!!$occassion!!}@endtypography
        @if(!$eventIsInThePast)
            @button([
                'href' => $icsDownloadLink,
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
    @endiconSection

    @if(!empty($bookingLink) && !$eventIsInThePast)
        @iconSection(['icon' => ['icon' => 'local_activity', 'size' => 'md']])
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
        @endiconSection
    @endif

    @if(!empty($occassions))
        @iconSection(['icon' => ['icon' => 'calendar_month', 'size' => 'md']])
            @include('partials.post.schema.event.icon-section-header', ['header' => $lang->moreOccassions])
            @element(['componentElement' => 'ul'])
                @foreach($occassions as $occassion)
                    @element(['componentElement' => 'li'])
                        @typography(){!!$occassion!!}@endtypography
                    @endelement
                @endforeach
            @endelement
        @endiconSection
    @endif

    @if(!empty($physicalAccessibilityFeatures))

        @iconSection(['icon' => ['icon' => 'accessibility', 'size' => 'md']])
            @include('partials.post.schema.event.icon-section-header', ['header' => $lang->accessibilityTitle])
            @element(['componentElement' => 'ul'])
                @foreach($physicalAccessibilityFeatures as $feature)
                    @element(['componentElement' => 'li']){!! $feature !!}@endelement
                @endforeach
            @endelement
        @endiconSection
    @endif


@stop