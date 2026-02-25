@extends('templates.grid', ['helperNavBeforeContent' => false])

@section('hero-top-sidebar')
    @include('partials.schema.project.title-area')
    @include('partials.navigation.helper', [
        'classList' => ['o-container', 'o-container--helper-nav'],
    ])
    @parent
@stop

@section('layout')
    @section('content')
        @include('partials.schema.project.description')
    @stop

    @section('sidebar-right-content')
        @include('partials.schema.project.information-list')
    @stop

    @include('templates.sections.grid.content', [
        'addToArticleClassList' => ['c-article', 'c-article--readable-width']
    ])
@stop
