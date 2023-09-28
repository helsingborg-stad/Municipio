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
    
    @if ($quickFacts)
        @paper(['classList' => ['u-color__bg--default', 'u-padding--4'] ])
            
            @typography(['element' => 'h3', 'variant' => 'h3'])
                {{$quickFactsTitle}}
            @endtypography

            @listing([
                'list' => $quickFacts,
                'elementType' => 'ul'
            ])

            @endlisting
        @endpaper
    @endif

    @if ($contacts)
        @foreach ($contacts as $contact)

            @card([])

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
    @endif

    @accordion(['list'=> $accordionData])
    @endaccordion

    @if($visitingDataTitle)

        @typography(['element' => 'h3', 'variant' => 'h3'])
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
                    <div class="o-grid-4@md">
                        @typography(['element' => 'p', 'variant' => 'p', 'classList' => ['u-margin__top--0']])
                            {{$visitingAddress->name}}<br/>
                            {{$visitingAddress->post_code}} {{$visitingAddress->city}}
                        @endtypography
                    </div>
                @endforeach

            </div>
        @endif

        @openStreetMap([
            'startPosition' => $visitingAddressMapStartPosition,
            'pins' => $visitingAddressMapPins,
            'height' => '60vh',
        ])
        @endopenStreetMap

    @endif



@stop