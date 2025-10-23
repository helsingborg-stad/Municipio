@extends('templates.grid')

{{-- Main content area --}}
@section('layout')
    @include('templates.sections.grid.above-content')

    @section('before-content')
        @include('partials.schema.event.title-area')
    @stop

    @section('content')
        @include('partials.schema.event.description')
        @include('partials.schema.event.accessibility-features')
    @stop

    @section('sidebar-right-content')
        @include('partials.schema.event.place-card')
        @include('partials.schema.event.occassions-card')
        @include('partials.schema.event.booking-link-card')
    @stop

    @include('templates.sections.grid.content')
@stop