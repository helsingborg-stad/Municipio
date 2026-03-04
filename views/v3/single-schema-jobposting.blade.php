@extends('templates.grid')

@section('layout')
    @section('before-content')
        @include('partials.schema.jobposting.title-area')
    @stop
    @section('content')
        @include('partials.schema.jobposting.description')
        @includeWhen($post->getSchemaProperty('hiringOrganization')['ethicsPolicy'] ?? null, 'partials.schema.jobposting.ethics-policy')
    @stop

    @section('sidebar-right-content')
        @includeWhen(!empty($informationList), 'partials.schema.jobposting.information-list')
        @includeWhen(!empty($post->getSchemaProperty('applicationContact')), 'partials.schema.jobposting.application-contact')
        @includeWhen($post->getSchemaProperty('url'), 'partials.schema.jobposting.apply-button')
    @stop

    @include('templates.sections.grid.content', [
        'addToArticleClassList' => ['c-article', 'c-article--readable-width']
    ])
@stop
