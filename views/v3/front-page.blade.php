@extends('templates.master')
@section('layout')
    <div class="o-container">
        @includeIf('partials.sidebar', ['id' => 'content-area', 'classes' => ['o-grid']])
        @includeIf('partials.sidebar', ['id' => 'content-area-bottom', 'classes' => ['o-grid']])
    </div>
@stop