@extends('templates.grid')

@section('layout')
        @section('before-content')
            @include('partials.schema.preschool.title-area')
            @includeWhen(!empty($sliderItems), 'partials.schema.preschool.slider')
        @stop
        @section('content')
            @includeWhen(!empty($usps), 'partials.schema.preschool.usps')
            @includeWhen(!empty($events), 'partials.schema.preschool.events')
            @includeWhen(!empty($visitUs), 'partials.schema.preschool.visit-us')
            @includeWhen(!empty($actions), 'partials.schema.preschool.actions')
            @includeWhen(!empty($accordionListItems), 'partials.schema.preschool.accordion-list')
            @includeWhen(!empty($contactPoints), 'partials.schema.preschool.contact-points')
            @includeWhen(!empty($personsAttributes), 'partials.schema.preschool.contact-persons')
            @includeWhen(!empty($addresses), 'partials.schema.preschool.addresses')
    @stop

    @include('templates.sections.grid.content', [
        'addToArticleClassList' => ['c-article', 'c-article--readable-width']
    ])
@stop
