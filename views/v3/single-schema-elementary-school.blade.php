@extends('templates.grid')

@section('layout')
        @section('before-content')
        @endsection
        @section('content')
            @include('partials.schema.elementary-school.title-area')
            @includeWhen(!empty($sliderItems), 'partials.schema.elementary-school.slider')
            @includeWhen(!empty($usps), 'partials.schema.elementary-school.usps')
            @includeWhen(!empty($events), 'partials.schema.elementary-school.events')
            @includeWhen(!empty($actions), 'partials.schema.elementary-school.actions')
            @includeWhen(!empty($accordionListItems), 'partials.schema.elementary-school.accordion-list')

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

        @if(!empty($addresses))
            @paper(['classList' => ['u-padding--2']])
                @element()
                    @typography(['element' => 'h2'])
                        {!! $lang->addressLabel !!}
                    @endtypography
                    @foreach ($addresses as $address)
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
                    @endforeach

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

    @include('templates.sections.grid.content', [
        'addToArticleClassList' => ['c-article', 'c-article--readable-width']
    ])
@stop
