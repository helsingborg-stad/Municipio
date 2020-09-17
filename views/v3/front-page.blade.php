@extends('templates.master')
@section('layout')
    <div class="o-container">
        @includeIf('partials.sidebar', ['id' => 'content-area', 'classes' => ['o-row']])
        @includeIf('partials.sidebar', ['id' => 'content-area-bottom', 'classes' => ['o-row']])
    </div>
@stop