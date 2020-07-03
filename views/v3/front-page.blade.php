@extends('templates.master')
@section('layout')
    @includeIf('partials.sidebar', ['id' => 'content-area'])
    @includeIf('partials.sidebar', ['id' => 'content-area-bottom'])
@stop