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

    {!! wpautop( nl2br(e($post->getSchemaProperty('description')))) !!}
    
@stop

@section('sidebar.right-sidebar.before')
    @paper(['classList' => ['u-margin__bottom--4']])
        @collection()
            @collection__item([])
                @typography(['element' => 'h4', 'variant' => 'h4'])
                    {!! $lang->dateLabel !!}
                @endtypography
                @typography([])
                    {!!$occasion!!}
                @endtypography
            @endcollection__item
        @endcollection

        @if(!empty($openingHours))
            @collection()
                @collection__item([])
                    @typography(['element' => 'h4', 'variant' => 'h4'])
                        {!! $lang->openingHoursLabel !!}
                    @endtypography
                    @typography([])
                        {!! $openingHours !!}
                    @endtypography
                @endcollection__item
            @endcollection
        @endif

        @if(!empty($specialOpeningHours))
            @collection()
                @collection__item([])
                    @typography(['element' => 'h4', 'variant' => 'h4'])
                        {!! $lang->specialOpeningHoursLabel !!}
                    @endtypography
                    @typography([])
                        @foreach ($specialOpeningHours as $line)
                            {!! $line !!}
                        @endforeach
                    @endtypography
                @endcollection__item
            @endcollection
        @endif

        @collection(['classList' => ['u-display--flex']])
            @collection__item()
                @typography(['element' => 'h4', 'variant' => 'h4'])
                    {!!$lang->placeTitle!!}
                @endtypography
                @typography([])
                    {!!$placeName!!}<br>
                    {!!$placeAddress!!}
                @endtypography
            @endcollection__item
        @endcollection


        @if(!empty($priceListItems))
            @collection()
                    @collection__item([])
                        @typography(['element' => 'h4', 'variant' => 'h4'])
                            {!! $lang->entranceLabel !!}
                        @endtypography
                        @foreach ($priceListItems as $priceListItem)
                            @element([])
                                @element(['componentElement' => 'span']){!!$priceListItem->getName() !!}: @endelement
                                @element(['componentElement' => 'span', 'classList' => ['u-float--right']]){!! $priceListItem->getPrice() !!}@endelement
                            @endelement
                        @endforeach
                    @endcollection__item
            @endcollection
        @endif
    
    @endpaper

@stop

@section('below')
    @if(!empty($galleryComponentAttributes))
        @typography(['element' => 'h3'])
            {!! $lang->galleryLabel !!}
        @endtypography
        @gallery([...$galleryComponentAttributes, 'classList' => ['u-margin__bottom--6', 'u-margin__top--4']])@endgallery
    @endif
    @includeIf('partials.sidebar', ['id' => 'content-area-bottom', 'classes' => ['o-grid']])
@stop