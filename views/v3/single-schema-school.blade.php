@extends('templates.single')

@section('article.title.after')

    @if ($notification)
        @notice([
            'type' => 'warning',
            'message' => [
                'title' => $notification['title'] ?? '',
                'text' => $notification['text'] ?? ''
            ]
        ])
        @endnotice
    @endif

    @if ($post->excerpt)
        @typography([
            'classList' => ['u-margin__bottom--0', 'u-padding__bottom--0', 'u-margin__top--0', 'u-padding__top--0'],
            'element' => 'div'
        ])
            {!! $post->excerpt !!}
        @endtypography
    @endif

    @if ($facadeSliderItems)
        @slider(['showStepper' => true, 'autoSlide' => false])
            @foreach ($facadeSliderItems as $sliderItem)
                @slider__item($sliderItem)
                @endslider__item
            @endforeach
        @endslider
    @endif

    @if ($quickFacts)
        @paper(['classList' => ['u-color__bg--complementary-lighter', 'u-padding--4']])
            @typography(['element' => 'h2', 'variant' => 'h2', 'classList' => ['u-margin__bottom--2']])
                {{ $quickFactsTitle }}
            @endtypography

            <div class="o-grid o-grid--no-gutter">
                @foreach ($quickFacts as $column)
                    <ul class="o-grid-4@md u-margin__top--0">
                        @foreach ($column as $listItem)
                            <li class="u-margin__top--1">{{ $listItem['label'] }}</li>
                        @endforeach
                    </ul>
                @endforeach
            </div>
        @endpaper
    @endif

    @if ($eventsTitle)
        @typography(['element' => 'h4'])
            {{ $eventsTitle }}
        @endtypography
    @endif

    @if ($events)

        @foreach ($events as $event)
            @collection([])
                @collection__item([
                    'classList' => ['u-box-shadow--3', 'u-margin__bottom--3', 'u-padding--1', 'u-border--1']
                ])
                    @slot('before')
                        @datebadge([
                            'classList' => ['u-padding--2', 'u-margin__right--2'],
                            'date' => $event['date'],
                            'size' => 'sm'
                        ])
                        @enddatebadge
                    @endslot
                    @typography(['element' => 'h3'])
                        {{ $event['title'] }}
                    @endtypography
                    @typography(['classList' => ['u-padding__bottom--2', 'u-padding__top-2']])
                        {{ $event['dateAndTime'] }}
                    @endtypography
                    {!! $event['text'] !!}
                @endcollection__item
            @endcollection
        @endforeach
    @endif

    @if ($visitUs)
        @typography(['element' => 'h2'])
            {{ $visitUs['title'] }}
        @endtypography

        @typography(['element' => 'p'])
        {!! $visitUs['content'] !!}
        @endtypography
    @endif


    @if ($application['displayOnWebsite'])

        @paper(['classList' => ['u-color__bg--complementary-lighter', 'u-padding--4']])
            @typography(['element' => 'h2'])
                {{ $application['title'] }}
            @endtypography

            @typography(['element' => 'p', 'classList' => ['u-margin__bottom--3']])
                {{ $application['description'] }}
            @endtypography

            <div class="o-grid o-grid--half-gutter">
                @if ($application['apply'])
                    @button([
                        'text' => $application['apply']['text'],
                        'href' => $application['apply']['url'],
                        'color' => 'primary',
                        'style' => 'filled',
                        'size' => 'md',
                        'classList' => ['o-grid-6@md']
                    ])
                    @endbutton
                @endif

                @if ($application['howToApply'])
                    @button([
                        'text' => $application['howToApply']['text'],
                        'href' => $application['howToApply']['url'],
                        'color' => 'secondary',
                        'style' => 'filled',
                        'size' => 'md',
                        'classList' => ['o-grid-6@md', 'u-margin__left--0']
                    ])
                    @endbutton
                @endif
            </div>
        @endpaper
    @endif

    @if ($contacts)

        @typography(['element' => 'h2', 'variant' => 'h2'])
            {{ $contactTitle }}
        @endtypography

        <div class="o-grid">
            @foreach ($contacts as $contact)
                @card(['classList' => ['o-grid-4@md', 'u-color__bg--transparent']])
                    @if ($contact->imageSrc)
                        @image([
                            'src' => $contact->imageSrc
                        ])
                        @endimage
                    @endif

                    @typography(['element' => 'h3', 'variant' => 'h3', 'classList' => ['u-margin__top--1', 'u-margin__bottom--0']])
                        {{ $contact->name }}
                    @endtypography

                    @typography(['element' => 'p', 'classList' => ['u-margin__top--0']])
                        {{ $contact->professionalTitle }}
                    @endtypography

                    @link(['href' => "tel:{$contact->phone}"])
                        {{ $contact->phone }}
                    @endlink

                    @link(['href' => "mailto:{$contact->email}"])
                        {{ $contact->email }}
                    @endlink
                @endcard
            @endforeach
        </div>
    @endif

    @if ($visitingDataTitle)

        @typography(['element' => 'h2', 'variant' => 'h2'])
            {{ $visitingDataTitle }}
        @endtypography

        @if ($visitingAddresses)
            <div class="o-grid">

                @foreach ($visitingAddresses as $visitingAddress)
                    @if( sizeof($visitingAddresses) === 1 )
                        <div class="o-grid-12@md">
                    @else
                        <div class="o-grid-4@md">
                    @endif
                        @typography(['element' => 'p', 'classList' => ['u-margin__top--0']])
                            @if ($visitingAddress['description'])
                                <strong>{{ $visitingAddress['description'] }}</strong>
                                <br/>
                            @endif
                            {!! $visitingAddress['address'] !!}
                            <br>
                            @link([
                                'href' => $visitingAddress['mapsLink']['href'],
                            ])
                                {{$visitingAddress['mapsLink']['text']}}
                            @endlink
                        @endtypography
                    </div>
                @endforeach

            </div>
        @endif
    @endif

    @if (!empty($accordionListItems))

        @accordion([
            'classList' => ['u-margin__top--5']
        ])
            @foreach ($accordionListItems as $listItem)
                @accordion__item([
                    'beforeHeading' => '<span id="' . $listItem['anchor'] . '">',
                    'afterHeading' => '</span>',
                    'heading' => $listItem['heading'],
                    'classList' => ['u-color__bg--lightest', 'u-box-shadow--3', 'u-margin__bottom--1', 'u-padding--2', 'u-border--0'],
                ])
                    {!! $listItem['content'] !!}
                @endaccordion__item
            @endforeach
        @endaccordion

    @endif

    @if ($pages)
        @collection(['classList' => ['o-grid', 'o-grid--half-gutter']])
            @foreach ($pages as $page)
                @collection__item([
                    'containerAware' => true,
                    'link' => $page['link'],
                    'classList' => [
                        'o-grid-' . $pagesNumberOfColumns . '@md',
                        'u-color__bg--primary',
                        'u-box-shadow--3',
                        'u-padding--2'
                    ]
                ])
                    @typography([
                        'element' => 'h3', 
                        'variant' => 'h4',
                        'classList' => ['u-color__text--primary-contrasting']
                    ])
                        {{ $page['title'] }}
                    @endtypography

                    @slot('secondary')
                        @icon([
                            'icon' => 'arrow_forward', 
                            'size' => 'md', 
                            'classList' => ['u-color__text--primary-contrasting']
                        ])
                        @endicon
                    @endslot

                @endcollection__item
            @endforeach
        @endcollection
    @endif

    @if ($socialMediaLinks)

        @typography(['element' => 'h2', 'variant' => 'h2'])
            {{ $socialMediaLinksTitle }}
        @endtypography

        <div>

            @foreach ($socialMediaLinks as $socialMediaLink)
                @button([
                    'text' => $socialMediaLink['text'],
                    'color' => 'primary',
                    'style' => 'basic',
                    'size' => 'lg',
                    'href' => $socialMediaLink['href'],
                    'icon' => $socialMediaLink['icon'],
                    'reversePositions' => 'true',
                    'classList' => ['u-margin__right--3'],
                ])
                @endbutton
            @endforeach
        </div>
    @endif

    @if ($visitingDataTitle)
        @openStreetMap([
            'startPosition' => $visitingAddressMapStartPosition,
            'pins' => $visitingAddressMapPins,
            'height' => '400px',
            'classList' => ['u-margin__top--5']
        ])
        @endopenStreetMap
    @endif

    @if ($gallerySliderItems)
        @slider(['showStepper' => true, 'autoSlide' => false, 'classList' => ['u-margin__top--6']])
            @foreach ($gallerySliderItems as $sliderItem)
                @slider__item($sliderItem)
                @endslider__item
            @endforeach
        @endslider
    @elseif($video)
        <div class="u-margin__top--6">
        {!! $video !!}
        </div>
    @endif

@stop
