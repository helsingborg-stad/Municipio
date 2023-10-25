@extends('templates.single')

    @section('article.title.after')

    @if($notification)
        @notice([
            'type' => 'info',
            'message' => [
                'title' => $notification['title'] ?? '',
                'text' => $notification['text'] ?? ''
            ]
        ])
        @endnotice
    @endif
    
    @if ($post->postExcerpt)
        {!! $post->postExcerpt !!}
    @endif

    @if($facadeSliderItems)
        @slider(['showStepper' => true,'autoSlide' => false])
            @foreach ($facadeSliderItems as $sliderItem)
                @slider__item($sliderItem)@endslider__item
            @endforeach
        @endslider
    @endif
    
    @if ($quickFacts)
        @paper(['classList' => ['u-color__bg--complementary-lighter', 'u-padding--4'] ])
            
            @typography(['element' => 'h2', 'variant' => 'h2', 'classList' => ['u-margin__bottom--2']])
                {{$quickFactsTitle}}
            @endtypography

            <div class="o-grid o-grid--no-gutter">
                @foreach($quickFacts as $column)
                    <ul class="o-grid-4@md u-margin__top--0">
                        @foreach($column as $listItem)
                            <li class="u-margin__top--1">{{$listItem['label']}}</li>
                        @endforeach
                    </ul>
                @endforeach
            </div>

        @endpaper
    @endif


    @paper(['classList' => ['u-color__bg--complementary-lighter', 'u-padding--4'] ])
        
        @typography(['element' => 'h2'])
            {{$application['title']}}
        @endtypography

        @typography(['element' => 'p', 'classList' => ['u-margin__bottom--3']])
            {{$application['description']}}
        @endtypography

        @button([
            'text' => $application['apply']['text'],
            'href' => $application['apply']['url'],
            'color' => 'primary',
            'style' => 'filled',
            'size' => 'lg'
        ])
        @endbutton
        
        @button([
            'text' => $application['howToApply']['text'],
            'href' => $application['howToApply']['url'],
            'color' => 'secondary',
            'style' => 'filled',
            'size' => 'lg',
        ])
        @endbutton

    @endpaper

    @if ($contacts)

        @typography(['element' => 'h2', 'variant' => 'h2'])
            {{$contactTitle}}
        @endtypography

        <div class="o-grid">
            @foreach ($contacts as $contact)

                @card(['classList' => ['o-grid-4@md', 'u-color__bg--transparent']])

                    @if($contact->attachment)
                        @image([
                            'src'=> $contact->attachment->guid
                        ])
                        @endimage
                    @endif

                    @typography(['element' => 'h3', 'variant' => 'h3', 'classList' => ['u-margin__top--1', 'u-margin__bottom--0']])
                        {{$contact->name}}
                    @endtypography

                    @typography(['element' => 'p', 'variant' => 'p', 'classList' => ['u-margin__top--0']])
                        {{$contact->professionalTitle}}
                    @endtypography

                    @link(['href' => "tel:{$contact->phone}"])
                        {{$contact->phone}}
                    @endlink
                    
                    @link(['href' => "mailto:{$contact->email}"])
                        {{$contact->email}}
                    @endlink

                @endcard

            @endforeach

    @endif

    @if($visitingDataTitle)

        @typography(['element' => 'h2', 'variant' => 'h2'])
            {{$visitingDataTitle}}
        @endtypography
        
        @if($visitingAddresses)
            <div class="o-grid">

                @foreach ($visitingAddresses as $visitingAddress)
                    <div class="o-grid-3@md">
                        @typography(['element' => 'p', 'variant' => 'p', 'classList' => ['u-margin__top--0']])
                            @if($visitingAddress['description'])
                                {{$visitingAddress['description']}}<br/>
                            @endif
                            {!!$visitingAddress['address']!!}
                            @link([
                                'href' => $visitingAddress['mapsLink']['href']
                            ])
                                {{$visitingAddress['mapsLink']['text']}}
                            @endlink
                        @endtypography
                    </div>
                @endforeach

            </div>
        @endif
    @endif

    @if(!empty($accordions))
        <div class="o-grid o-grid--half-gutter">
            @foreach ($accordions as $accordion)
                @accordion([
                    'list'=> $accordion['list'], 'classList' => ['u-color__bg--lightest', 'u-box-shadow--3']])
                @endaccordion
            @endforeach
        </div>
    @endif
    
    @if( $socialMediaLinks )

        <div>
        
            @typography(['element' => 'h2', 'variant' => 'h2'])
                {{$socialMediaLinksTitle}}
            @endtypography

            @foreach ($socialMediaLinks as $socialMediaLink)
                @button([
                    'text' => $socialMediaLink['text'],
                    'color' => 'primary',
                    'style' => 'basic',
                    'size' => 'lg',
                    'href' => $socialMediaLink['href'],
                    'icon' => $socialMediaLink['icon'],
                    'reversePositions' => 'true',
                    'classList' => ['u-margin__right--3']
                ])
                @endbutton
            @endforeach
        </div>
    @endif
    


    @if($gallerySliderItems)
        @slider(['showStepper' => true,'autoSlide' => false])
            @foreach ($gallerySliderItems as $sliderItem)
                @slider__item($sliderItem)@endslider__item
            @endforeach
        @endslider
    @elseif($video)
        @iframe([
            'src' => $video,
            'height' => 600,
            'labels' => ['knownLabels' => ['button' => '', 'title' => '', 'info' => ''], 'unknownLabels' => ['button' => '', 'title' => '', 'info' => '']],
        ])
        @endiframe
    @endif

    @if($pages)
        @collection(['classList' => ['o-grid']])
            @foreach ($pages as $page)
                @collection__item([
                    'containerAware' => true,
                    'link' => $page['link'],
                    'classList' => [ 'o-grid-6@md', 'u-color__bg--lightest', 'u-box-shadow--3', 'u-padding--4']
                ])
                    @typography(['element' => 'h4'])
                    {{$page['title']}}
                    @endtypography
                    {!!$page['content']!!}
                @endcollection__item
            @endforeach
        @endcollection
    @endif

    @if($visitingDataTitle)

        @openStreetMap([
            'startPosition' => $visitingAddressMapStartPosition,
            'pins' => $visitingAddressMapPins,
            'height' => '60vh',
        ])
        @endopenStreetMap

    @endif

@stop