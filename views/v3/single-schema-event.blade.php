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

    <div class="u-display--flex u-flex-direction--column u-flex--gridgap">

        @if(!empty($bookingLink))

            <div>
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
                            <small>{!!$lang->bookingDisclaimer!!}</small>
                        @endcollection__item
                    @endcollection
                @endpaper
            </div>
        @endif
        <div>
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
                                    @element(['componentElement' => 'div', 'classList' => ['u-display--grid', 'u-gap-2']])
                                        @foreach($dateAndTimeForEventsInSameSeries as $event)

                                                <div>
                                                    <strong>{!!$event['local']!!}</strong>
                                                    @typography(){!!$event['time']!!}@endtypography
                                                </div>

                                        @endforeach
                                    @endelement
                                @endaccordion__item
                            @endaccordion
                        @endcollection__item
                    @endif
                @endcollection
            @endpaper
        </div>

        @if(!empty($placeUrl) && !empty($placeName) && !empty($placeAddress))
            <div>
                @include('partials.post.schema.event.sidebar-header', ['icon' => 'location_on', 'header' => $lang->placeTitle])
                
                @paper(['padding' => 2])
                    @collection()
                        @collection__item

                            <strong>{!!$placeName!!}</strong>
                            <address>
                                @link(['href' => $placeUrl])
                                    {!!$placeAddress!!}
                                @endlink
                            </address>
                            
                        @endcollection__item
                    @endcollection
                @endpaper
            </div>
        @endif

        @if(!empty($priceListItems))
            <div>
                @include('partials.post.schema.event.sidebar-header', ['icon' => 'payments', 'header' => $lang->priceTitle]) <!-- Updated to use lang variable -->
                @paper(['padding' => 2])
                    @collection()
                        @foreach ($priceListItems as $priceListItem)
                            @collection__item()
                                <div>
                                    <strong>{!! $priceListItem->getName() !!}</strong>
                                    <span>{!! $priceListItem->getPrice() !!}</span>
                                </div>
                            @endcollection__item
                        @endforeach
                    @endcollection
                @endpaper
            </div>
        @endif

        @if(!empty($organizers))

            <div>
                @include('partials.post.schema.event.sidebar-header', ['icon' => 'info', 'header' => $lang->organizersTitle]) <!-- Updated to use lang variable -->

                @paper(['padding' => 2, 'classList' => ['u-margin__top--1', 'u-padding--4']])
                    @foreach($organizers as $organizer)
                            @if(!empty($organizer['name'])) 

                                <div>
                                    @typography(['element' => 'h3', 'classList' => ['u-margin__bottom--2']]){!!$organizer['name']!!}@endtypography

                                    @if(!empty($organizer['url']))
                                        @element(['componentElement' => 'div', 'classList' => ['u-width--100', 'u-truncate', 'u-margin__bottom--1']])
                                            @icon(['icon' => 'language'])@endicon @link(['href' => $organizer['url']]) {!!$organizer['url']!!} @endlink
                                        @endelement
                                    @endif
                                    
                                    @if(!empty($organizer['email']))
                                        @element(['componentElement' => 'div', 'classList' => ['u-width--100', 'u-truncate', 'u-margin__bottom--1']])
                                            @icon(['icon' => 'email'])@endicon @link(['href' => 'mailto:'.$organizer['email']]) {!!$organizer['email']!!} @endlink
                                        @endelement
                                    @endif
                                    @if(!empty($organizer['telephone']))
                                        @element(['componentElement' => 'div', 'classList' => ['u-width--100', 'u-truncate']])
                                            @icon(['icon' => 'phone'])@endicon @link(['href' => 'tel:'.$organizer['telephone']]) {!!$organizer['telephone']!!} @endlink
                                        @endelement
                                    @endif
                                    

                                </div>

                            @endif
                        @endforeach
                @endpaper
            </div>
        @endif

        @if(!empty($physicalAccessibilityFeatures))

            <div>
                @include('partials.post.schema.event.sidebar-header', ['icon' => 'info', 'header' => $lang->accessibilityTitle])

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
            </div>
        @endif

    </div>

@stop