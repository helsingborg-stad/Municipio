@extends('templates.single', ['showSidebars' => true, 'centerContent' => true])

@section('article.content')

    @section('article.title.before')
        <div class="u-display--flex u-flex--gridgap u-flex-direction--column">
    @stop

    @section('article.title.after')

        @element(['classList' => ['lead']])
            {!! $preamble !!}
        @endelement

        @if(!empty($post->getImage()))
            @image([
                'src' => $post->getImage(),
                'fullWidth' => true
            ])
            @endimage
        @endif

        @if ($usps)
            @paper(['classList' => ['u-padding--2']])
                @typography(['element' => 'h2', 'variant' => 'h2', 'classList' => ['u-margin__bottom--2']])
                    {!! $lang->uspsLabel !!}
                @endtypography

                @element(['classList' => ['o-grid', 'o-grid--no-gutter']])
                    @foreach ($usps as $uspColumn)
                        <ul class="o-grid-4@md u-margin__top--0">
                            @foreach ($uspColumn as $uspItem)
                                <li class="u-margin__top--1">{{ $uspItem }}</li>
                            @endforeach
                        </ul>
                    @endforeach
                @endelement
            @endpaper
        @endif

        @if (!empty($actions))
            @paper(['classList' => ['u-color__bg--complementary-lighter', 'u-padding--2']])
                @typography(['element' => 'h2'])
                    {!! $lang->actionsLabel !!}
                @endtypography
                @if(!empty($actions['description']))
                    @typography(['element' => 'p', 'classList' => ['u-margin__bottom--3']])
                        {!! $actions['description'] !!}
                    @endtypography
                @endif
                @element(['classList' => ['o-grid', 'o-grid--half-gutter']])
                    @foreach ($actions['buttonsArgs'] as $buttonArgs)
                        @element(['classList' => ['o-grid-12@sm', 'o-grid-6@md']])
                            @button([...$buttonArgs, 'classList' => ['u-width--100']])@endbutton
                        @endelement
                    @endforeach
                @endelement
            @endpaper
        @endif
        
        @if(!empty($accordionListItems))
            @accordion(['list' => $accordionListItems, 'spacedSections' => true])
            @endaccordion
        @endif

        @if(!empty($sliderImages))
            @slider(['repeatSlide' => true, 'autoSlide' => false,'padding' => 11, 'showStepper' => false])
                @foreach($sliderImages as $image)
                    @slider__item([
                        'image' => $image,  
                    ])
                    @endslider__item
                @endforeach
            @endslider
        @endif

        @if(!empty($contactPoints))
            @paper(['classList' => ['u-padding--2']])
                @typography(['element' => 'h2', 'classList' => ['u-margin__bottom--2']])
                    {!! $lang->contactPointsLabel !!}
                @endtypography
                @foreach($contactPoints['items'] as $item)
                    @button([
                        'text' => $item['name'],
                        'color' => 'primary',
                        'size' => 'lg',
                        'href' => $item['url'],
                        'icon' => $item['icon'],
                        'reversePositions' => 'true',
                        'classList' => ['u-margin__right--1'],
                    ])
                    @endbutton
                @endforeach
            @endpaper
        @endif

        @if(!empty($personsAttributes))
            @element()
                @typography(['element' => 'h2'])
                    {!! $lang->contactLabel !!}
                @endtypography
                @element(['classList' => ['o-grid', 'o-grid--half-gutter', 'u-margin__top--2']])
                    @foreach ($personsAttributes as $personAttributes)
                        @person(array_merge($personAttributes, ['classList' => ['o-grid-12@sm', 'o-grid-6@md']]))@endperson
                    @endforeach
                @endelement
            @endelement
        @endif

        @if(!empty($address))
            @paper(['classList' => ['u-padding--2']])
                @element()
                    @typography(['element' => 'h2'])
                        {!! $lang->addressLabel !!}
                    @endtypography
                    @if(!empty($address['address']))
                        @typography()
                            {!! $address['address'] !!}
                        @endtypography
                    @endif
                    @if(!empty($address['directionsLink']))
                        @link(['href' => $address['directionsLink']['href']])
                            {!! $address['directionsLink']['label'] !!}
                        @endlink
                    @endif
                    @openStreetMap([
                        ...$mapAttributes,
                        'height' => '400px',
                        'classList' => ['u-margin__top--2']
                    ])
                    @endopenStreetMap
                @endelement
            @endpaper
        @endif

    @stop

    </div>{{-- Closes div opened above in article.title.before --}}
    
@stop