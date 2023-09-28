@extends('templates.single')

@section('content')
    
    @if ($post->postTitleFiltered)
        @group([
            'justifyContent' => 'space-between'
        ])
            @if ($post->postTitleFiltered)
                @typography([
                    'element' => 'h1', 
                    'variant' => 'h1', 
                    'id' => 'page-title',
                ])
                    {!! $post->postTitleFiltered !!}
                @endtypography
            @endif
        @endgroup
    @endif

    @slider([
        'showStepper' => true,
        'autoSlide' => false,
    ])
        @foreach ($facadeImages as $facadeImage)
            @slider__item([
                'title' => '',
                'layout' => 'center',
                'containerColor' => 'transparent',
                'textColor' => 'white',
                'desktop_image' => $facadeImage['src'],
                'heroStyle' => true
            ])
            @endslider__item
        @endforeach

    @endslider
    
    @if ($quickFacts)
        @paper(['classList' => ['u-color__bg--default', 'u-padding--4'] ])
            
            @typography(['element' => 'h2', 'variant' => 'h2'])
                {{$quickFactsTitle}}
            @endtypography

            <ul class="o-grid">
                @foreach($quickFacts as $quickFact)
                    <li class="o-grid-3@md">{{$quickFact['label']}}</li>
                @endforeach
            </ul>

        @endpaper
    @endif

    @if ($contacts)

        @typography(['element' => 'h2', 'variant' => 'h2'])
            {{$contactTitle}}
        @endtypography

        <div class="o-grid">
            @foreach ($contacts as $contact)

                @card(['classList' => ['o-grid-3@md']])

                    @typography(['element' => 'h3', 'variant' => 'h3'])
                        {{$contact->name}}
                    @endtypography

                    @typography(['element' => 'p', 'variant' => 'p'])
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
        </div>
    @endif

    @if($visitingDataTitle)

        @typography(['element' => 'h2', 'variant' => 'h2'])
            {{$visitingDataTitle}}
        @endtypography

        @if(sizeof($visitingAddresses) === 1)
            @typography(['element' => 'p', 'variant' => 'p', 'classList' => ['u-margin__top--0']])
                {{$visitingAddresses[0]->name}}<br/>
                {{$visitingAddresses[0]->post_code}} {{$visitingAddresses[0]->city}}
            @endtypography
        @endif
        
        @if(sizeof($visitingAddresses) > 1)
            <div class="o-grid">

                @foreach ($visitingAddresses as $visitingAddress)
                    <div class="o-grid-3@md">
                        @typography(['element' => 'p', 'variant' => 'p', 'classList' => ['u-margin__top--0']])
                            {{$visitingAddress->name}}<br/>
                            {{$visitingAddress->post_code}} {{$visitingAddress->city}}
                        @endtypography
                    </div>
                @endforeach

            </div>
        @endif
    @endif
    
    @if($aboutUsTitle)

        @typography(['element' => 'h2', 'variant' => 'h2'])
            {{$aboutUsTitle}}
        @endtypography

        {{$aboutUs}}
        
    @endif

    @slider([
        'showStepper' => true,
        'autoSlide' => false,
    ])
        @foreach ($images as $image)
            @slider__item([
                'title' => '',
                'layout' => 'center',
                'containerColor' => 'transparent',
                'textColor' => 'white',
                'desktop_image' => $image['src'],
                'heroStyle' => true
            ])
            @endslider__item
        @endforeach

    @endslider

    @accordion(['list'=> $accordionData])
    @endaccordion

    @if($pages)

        <div class="o-grid">
            @foreach ($pages as $page)

                @card(['classList' => ['o-grid-4@md', 'u-color__bg--default', 'u-padding--4']])

                    @typography(['element' => 'h3', 'variant' => 'h3'])
                        {{$page['title']}}
                    @endtypography

                    {!!$page['content']!!}

                    @button([
                        'text' => $page['linkText'],
                        'color' => 'primary',
                        'style' => 'filled',
                        'href' => $page['link'],
                        'classList' => ['u-margin__top--2']
                    ])@endbutton

                @endcard

            @endforeach
        </div>
    
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