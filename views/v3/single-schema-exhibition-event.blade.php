@extends('templates.grid', [
    'helperNavBeforeContent' => false,
    'layoutData' => [
        'addToDefaultClassList' => ['u-margin__bottom--4']
    ]
])

@section('hero-top-sidebar')
    @includeWhen($post->getImage(), 'partials.schema.exhibitionEvent.hero')
    @includeWhen($eventIsInThePast, 'partials.schema.exhibitionEvent.expired-notice')
    @include('templates.sections.grid.above-content')
    @parent
@stop

@section('layout')
    @includeWhen($helperNavBeforeContent, 'partials.navigation.helper')

    @section('content')
        @include('partials.schema.exhibitionEvent.title-area')
        @includeWhen($description, 'partials.schema.exhibitionEvent.description')
        @includeWhen($galleryComponentAttributes, 'partials.schema.exhibitionEvent.gallery')
    @stop

    @section('sidebar-right-content')
        @includeWhen($openingHours, 'partials.schema.exhibitionEvent.opening-hours')
        @includeWhen($specialOpeningHours, 'partials.schema.exhibitionEvent.special-opening-hours')
        @includeWhen($priceListItems, 'partials.schema.exhibitionEvent.price-list')
    @stop

    @include('templates.sections.grid.content')
@stop