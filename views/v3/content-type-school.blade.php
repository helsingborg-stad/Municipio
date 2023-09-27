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
        @card([
        ])
            @typography(['element' => 'h3', 'variant' => 'h3'])
                {{$quickFactsTitle}}
            @endtypography

            @listing([
                'list' => $quickFacts,
                'elementType' => 'ul'
            ])

            @endlisting
        @endcard
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

    @accordion(
    [
        'list'=> $accordionData
    ]
)
@endaccordion

@stop