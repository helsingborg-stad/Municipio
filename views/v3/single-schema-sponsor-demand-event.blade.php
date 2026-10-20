@extends('templates.grid', ['layoutData' => [
    'addToDefaultClassList' => ['o-container--content', 'u-margin__bottom--4']
]])

@section('hero-top-sidebar')
    @include('partials.navigation.helper', [
        'classList' => ['o-container', 'o-container--helper-nav'],
    ])
    @parent
@stop

@section('layout')
    @section('content')
        @include('partials.schema.sponsor.content')
    @stop

    @include('templates.sections.grid.content', [
        'addToArticleClassList' => ['c-article', 'c-article--readable-width', 's-article']
    ])
@stop
