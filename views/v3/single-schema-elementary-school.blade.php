@extends('templates.single', ['showSidebars' => false, 'centerContent' => true])

@section('article.content')

    @section('article.title.before')
        <div class="u-display--flex u-flex--gridgap u-flex-direction--column">
    @stop

    @section('article.title.after')

        @element()
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

                <div class="o-grid o-grid--no-gutter">
                    @foreach ($usps as $uspColumn)
                        <ul class="o-grid-4@md u-margin__top--0">
                            @foreach ($uspColumn as $uspItem)
                                <li class="u-margin__top--1">{{ $uspItem }}</li>
                            @endforeach
                        </ul>
                    @endforeach
                </div>
            @endpaper
        @endif
        
        @if(!empty($accordionListItems))
            @paper()
                @accordion(['list' => $accordionListItems])
                @endaccordion
            @endpaper
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
            @element()
                @typography(['element' => 'h2'])
                    {!! $lang->addressLabel !!}
                @endtypography
                @typography()
                    {!! $address !!}
                @endtypography
                @openStreetMap([
                    ...$mapAttributes,
                    'height' => '400px',
                    'classList' => ['u-margin__top--2']
                ])
                @endopenStreetMap
            @endelement
        @endif

    @stop

    </div>
    
@stop