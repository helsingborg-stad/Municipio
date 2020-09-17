@extends('templates.master')
@section('layout')
    @includeIf('partials.sidebar', ['id' => 'content-area', 'classes' => ['o-row']])
    @includeIf('partials.sidebar', ['id' => 'content-area-bottom', 'classes' => ['o-row']])
@stop