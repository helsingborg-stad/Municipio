@extends('templates.grid', ['layoutData' => [
    'addToDefaultClassList' => ['u-margin__y--4']
]])
{{-- Adding expire notice if needed --}}
@section('above-before')
    @include('partials.schema.event.expired-notice', ['classes' => []])
@endsection
{{-- Main content area --}}
@section('layout')
    @include('templates.sections.grid.above-content')

    @section('before-content')
        @include('partials.schema.event.title-area')
    @stop

    @section('content')
        @include('partials.schema.event.image')
        @include('partials.schema.event.description')
        @include('partials.schema.event.accessibility-features')
    @stop

    @section('sidebar-right-content')
        @include('partials.schema.event.place-card')
        @include('partials.schema.event.occassions-card')
        @include('partials.schema.event.booking-link-card')
        @includeWhen(!empty($organizers), 'partials.schema.event.organizers-card')
    @stop

    @include('templates.sections.grid.content')

    @includeWhen($postsListData['posts'], 'partials.schema.event.related-posts')
@stop