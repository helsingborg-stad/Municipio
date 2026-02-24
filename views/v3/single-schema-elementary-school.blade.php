@extends('templates.grid')

@section('layout')
        @section('before-content')
            @include('partials.schema.elementary-school.title-area')
            @includeWhen(!empty($sliderItems), 'partials.schema.elementary-school.slider')
        @stop
        @section('content')
            @includeWhen(!empty($usps), 'partials.schema.elementary-school.usps')
            @includeWhen(!empty($events), 'partials.schema.elementary-school.events')
            @includeWhen(!empty($actions), 'partials.schema.elementary-school.actions')
            @includeWhen(!empty($accordionListItems), 'partials.schema.elementary-school.accordion-list')
            @includeWhen(!empty($contactPoints), 'partials.schema.elementary-school.contact-points')
            @includeWhen(!empty($personsAttributes), 'partials.schema.elementary-school.contact-persons')
            @includeWhen(!empty($addresses), 'partials.schema.elementary-school.addresses')
        @stop

    @include('templates.sections.grid.content', [
        'addToArticleClassList' => ['c-article', 'c-article--readable-width']
    ])
@stop
