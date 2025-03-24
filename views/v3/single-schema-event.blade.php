@extends('templates.single')

@section('hero-top-sidebar')
    @hero([
        "image" => $post->imageContract,
        "size" => "small"
    ])
    @endhero
@stop

@section('article.content')
    
    {!!$post->schemaObject['description']!!}

    @button([
        'href' => $icsDownloadLink,
        'color' => 'primary',
        'style' => 'filled',
        'size' => 'md',
        'fullWidth' => false,
        'text' => $lang->addToCalendar,
        'icon' => 'calendar_add_on',
        'classList' => ['u-margin__top--4']
    ])
    @endbutton
    
@stop

@section('sidebar.right-sidebar.before')

    @element(['classList' => ['u-display--flex', 'u-flex-direction--column', 'u-flex--gridgap']])

        @if(!empty($bookingLink))

            @element([])
                @include('partials.post.schema.event.sidebar-header', ['icon' => 'local_activity', 'header' => $lang->bookingTitle])
                @paper(['padding' => 2])
                    @collection()
                        @collection__item()
                            @button([
                                'href' => $bookingLink,
                                'color' => 'primary',
                                'style' => 'filled',
                                'size' => 'lg',
                                'fullWidth' => true,
                                'text' => $lang->bookingButton,
                            ])
                            @endbutton
                            @element(['componentElement' => 'small']){!!$lang->bookingDisclaimer!!}@endelement
                        @endcollection__item
                    @endcollection
                @endpaper
            @endelement
        @endif
        @element([])
            @include('partials.post.schema.event.sidebar-header', ['icon' => 'schedule', 'header' => $lang->datesTitle])
            @paper(['padding' => 2])
                @collection()
                    @collection__item()
                        @element(['componentElement' => 'strong']){!!$dateAndTime['local']!!}@endelement
                        @typography(){!!$dateAndTime['time']!!}@endtypography
                    @endcollection__item
                    @if(!empty($dateAndTimeForEventsInSameSeries))
                        @collection__item()
                            @accordion(['attributeList' => ['style' =>  'margin: calc(var(--base, 8px)*-2);']])
                                @accordion__item([ 'heading' => $lang->moreDates ])
                                    @element(['classList' => ['u-display--grid', 'u-gap-2']])
                                        @foreach($dateAndTimeForEventsInSameSeries as $event)

                                                @element([])
                                                    @element(['componentElement' => 'strong']){!!$event['local']!!}@endelement
                                                    @typography(){!!$event['time']!!}@endtypography
                                                @endelement

                                        @endforeach
                                    @endelement
                                @endaccordion__item
                            @endaccordion
                        @endcollection__item
                    @endif
                @endcollection
            @endpaper
        @endelement

        @if(!empty($placeUrl) && !empty($placeName) && !empty($placeAddress))
            @element([])
                @include('partials.post.schema.event.sidebar-header', ['icon' => 'location_on', 'header' => $lang->placeTitle])
                
                @paper(['padding' => 2])
                    @collection()
                        @collection__item

                            @element(['componentElement' => 'strong']){!!$placeName!!}@endelement
                            @element(['componentElement' => 'address'])
                                @link(['href' => $placeUrl])
                                    {!!$placeAddress!!}
                                @endlink
                            @endelement
                            
                        @endcollection__item
                    @endcollection
                @endpaper
            @endelement
        @endif

        @if(!empty($priceListItems))
            @element([])
                @include('partials.post.schema.event.sidebar-header', ['icon' => 'payments', 'header' => $lang->priceTitle])
                @paper(['padding' => 2])
                    @collection()
                        @foreach ($priceListItems as $priceListItem)
                            @collection__item()
                                @element([])
                                    @element(['componentElement' => 'strong']){!! $priceListItem->getName() !!}@endelement
                                    @element(['componentElement' => 'span']){!! $priceListItem->getPrice() !!}@endelement
                                @endelement
                            @endcollection__item
                        @endforeach
                    @endcollection
                @endpaper
            @endelement
        @endif

        @if(!empty($organizers))

            @element([])
                @include('partials.post.schema.event.sidebar-header', ['icon' => 'info', 'header' => $lang->organizersTitle])

                @paper(['padding' => 2, 'classList' => ['u-margin__top--1', 'u-padding--4']])
                    @foreach($organizers as $organizer)
                            @if(!empty($organizer['name'])) 

                                @element([])
                                    @typography(['element' => 'h3', 'classList' => ['u-margin__bottom--2']]){!!$organizer['name']!!}@endtypography

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
                @endpaper
            @endelement
        @endif

        @if(!empty($physicalAccessibilityFeatures))

            @element([])
                @include('partials.post.schema.event.sidebar-header', ['icon' => 'accessibility', 'header' => $lang->accessibilityTitle])

                @paper(['padding' => 2, 'classList' => ['u-margin__top--1']])
                    @collection()
                        @collection__item
                            @element(['componentElement' => 'ul'])
                                @foreach($physicalAccessibilityFeatures as $feature)
                                    @element(['componentElement' => 'li']){!! $feature !!}@endelement
                                @endforeach
                            @endelement
                        @endcollection__item
                    @endcollection  
                @endpaper
            @endelement
        @endif

    @endelement

@stop