@extends('templates.grid', ['layoutData' => [
    'addToDefaultClassList' => ['u-margin__bottom--4']
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
        'addToArticleClassList' => ['c-article', 'u-width--100', 's-article']
    ])
@stop
