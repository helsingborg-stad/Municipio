@extends('templates.single')

@section('hero-top-sidebar')
    @image([ 'src'=> $post->imageContract, 'fullWidth' => true, ])@endimage
@stop

@section('article.content')
    
    

    @typography(['variant' => 'h2'])
        Beskrivning
    @endtypography
    
    {!!$post->schemaObject['description'] ?? ''!!}

    @button([
        'href' => $icsDownloadLink,
        'color' => 'primary',
        'style' => 'filled',
        'size' => 'md',
        'fullWidth' => false,
        'text' => 'Lägg till i kalender',
        'icon' => 'calendar_add_on',
        'classList' => ['u-margin__top--4']
    ])
    @endbutton
    
@stop

@section('sidebar.right-sidebar.before')

    <div class="u-display--flex u-flex-direction--column u-flex--gridgap">

        @if(!empty($bookingLink))

            <div>
                @include('partials.post.schema.event.sidebar-header', ['icon' => 'location_on', 'header' => 'Biljetter & anmälan'])
                @paper()
                    @collection()
                        @collection__item()
                            @button([
                                'href' => $bookingLink,
                                'color' => 'primary',
                                'style' => 'filled',
                                'size' => 'lg',
                                'fullWidth' => true,
                                'text' => 'Gå till bokningssidan'
                            ])
                            @endbutton
                            <small>Biljetter säljs enligt återförsäljare.</small>
                        @endcollection__item
                    @endcollection
                @endpaper
            </div>
        @endif

        <div>
            @include('partials.post.schema.event.sidebar-header', ['icon' => 'schedule', 'header' => 'Datum och tider'])
            @paper(['padding' => 2])
                @collection()
                    @collection__item()
                        @element(['componentElement' => 'strong']){!!$dateAndTime['local']!!}@endelement
                        @typography(){!!$dateAndTime['time']!!}@endtypography
                    @endcollection__item
                    @if(!empty($dateAndTimeForEventsInSameSeries))
                        @collection__item()
                            @typography(['variant' => 'h3'])
                                Övriga datum
                            @endtypography
                        @endcollection__item
                        @foreach($dateAndTimeForEventsInSameSeries as $event)
                            @collection__item()
                                <div>
                                    <strong>{!!$event['local']!!}</strong>
                                    @typography(){!!$event['time']!!}@endtypography
                                </div>
                            @endcollection__item
                        @endforeach
                    @endif
                @endcollection
            @endpaper
        </div>

        @if(!empty($placeUrl) && !empty($placeName) && !empty($placeAddress))
            <div>
                @include('partials.post.schema.event.sidebar-header', ['icon' => 'location_on', 'header' => 'Plats'])
                
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
                @include('partials.post.schema.event.sidebar-header', ['icon' => 'local_activity', 'header' => 'Priser'])
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
                @include('partials.post.schema.event.sidebar-header', ['icon' => 'info', 'header' => 'Organisatörer'])

                @paper(['padding' => 2, 'classList' => ['u-margin__top--1']])
                    @collection()
                    @foreach($organizers as $organizer)
                        @collection__item
                            <div>
                                <strong>{!!$organizer['name']!!}</strong><br>
                                @if(!empty($organizer['url']))
                                    @icon(['icon' => 'language'])@endicon @link(['href' => $organizer['url']]) {!!$organizer['url']!!} @endlink<br>
                                @endif
                                @if(!empty($organizer['email']))
                                    @icon(['icon' => 'email'])@endicon @link(['href' => 'mailto:'.$organizer['email']]) {!!$organizer['email']!!} @endlink<br>
                                @endif
                                @if(!empty($organizer['telephone']))
                                    @icon(['icon' => 'phone'])@endicon @link(['href' => 'tel:'.$organizer['telephone']]) {!!$organizer['telephone']!!} @endlink<br>
                                @endif
                            </div>
                            @endcollection__item
                        @endforeach
                    @endcollection  
                @endpaper
            </div>
        @endif
    </div>

@stop