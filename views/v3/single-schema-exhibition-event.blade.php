@extends('templates.single', ['showSidebars' => false, 'centerContent' => true])

@section('hero-top-sidebar')
    @hero([
        "image" => $post->getImage(),
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
    
    {!!$post->getSchemaProperty('description')!!}

    @iconSection()

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

        @if(!empty($occassions))
            @iconSection__item(['icon' => ['icon' => 'calendar_month', 'size' => 'md']])
                @include('partials.post.schema.event.icon-section-header', ['header' => $lang->moreOccassions])
                @element(['componentElement' => 'ul'])
                    @foreach($occassions as $occassion)
                        @element(['componentElement' => 'li'])
                            @typography(){!!$occassion!!}@endtypography
                        @endelement
                    @endforeach
                @endelement
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

    @endiconSection
    
@stop

@section('sidebar.right-sidebar.before')
    
    @paper(['padding' => 2])
        @collection()
            @collection__item([])
                @typography(['element' => 'h4'])
                    Datum
                @endtypography
                @typography([])
                    {!!$occassion!!}
                @endtypography
            @endcollection__item
        @endcollection
    @endpaper

    @paper(['padding' => 2])
        @collection(['classList' => ['u-display--flex']])
            @collection__item()
                @typography(['element' => 'h4'])
                    {!!$lang->placeTitle!!}
                @endtypography
                @typography([])
                    {!!$placeName!!}
                @endtypography
            @endcollection__item
        @endcollection
    @endpaper


    @if(!empty($priceListItems))
        @paper(['padding' => 2])
            @collection()
                    @collection__item([])
                        @typography(['element' => 'h4'])
                            Entré
                        @endtypography
                        @foreach ($priceListItems as $priceListItem)
                            @element([])
                                @element(['componentElement' => 'span']){!!$priceListItem->getName() !!}: @endelement
                                @element(['componentElement' => 'span', 'classList' => ['u-float--right']]){!! $priceListItem->getPrice() !!}@endelement
                            @endelement
                        @endforeach
                    @endcollection__item
            @endcollection
        @endpaper
    @endif

    {{-- @paper(['padding' => 2])
        @collection()
                @collection__item([])
                    @typography(['element' => 'h4'])
                        Planera ditt besök
                    @endtypography
                    @if(!empty($placeUrl))
                        @element(['classList' => ['u-width--100', 'u-truncate', 'u-margin__bottom--1']])
                            @icon(['icon' => 'language'])@endicon @link(['href' => $placeUrl]) Hitta hit @endlink
                        @endelement
                    @endif
                @endcollection__item
                @collection__item([])
                    @if(!empty($physicalAccessibilityFeatures))
                        @typography(['element' => 'h4'])
                            {{$lang->accessibilityTitle}}
                        @endtypography
                        @element(['componentElement' => 'ul'])
                            @foreach($physicalAccessibilityFeatures as $feature)
                                @element(['componentElement' => 'li']){!! $feature !!}@endelement
                            @endforeach
                        @endelement
                    @endif
                @endcollection__item
        @endcollection
    @endpaper --}}

@stop

@section('below')
    @typography(['element' => 'h3'])
        Galleri
    @endtypography
    @if(!empty($galleryComponentAttributes))
        @gallery($galleryComponentAttributes)@endgallery
    @endif
    @includeIf('partials.sidebar', ['id' => 'content-area-bottom', 'classes' => ['o-grid']])
@stop