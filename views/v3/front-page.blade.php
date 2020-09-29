@extends('templates.master')
@section('layout')
    <div class="o-container">
        @includeIf('partials.sidebar', ['id' => 'content-area', 'classes' => ['o-grid', 'o-grid--equal-elements']])
        @includeIf('partials.sidebar', ['id' => 'content-area-bottom', 'classes' => ['o-grid', 'o-grid--equal-elements']])
    </div>
@stop