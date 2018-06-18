@extends('templates.master')

@section('layout')

@if (is_active_sidebar('content-area'))
    <section class="creamy creamy-border-bottom  u-py-5 sidebar-content-area">
        <div class="container">
            @include('components.dynamic-sidebar', ['id' => 'content-area'])
        </div>
    </section>
@endif

@if (is_active_sidebar('content-area-bottom'))
    <div class="container u-py-5 sidebar-content-area-bottom">
        <div class="grid grid--columns">
                @include('components.dynamic-sidebar', ['id' => 'content-area-bottom'])
        </div>
    </div>
@endif
@stop
