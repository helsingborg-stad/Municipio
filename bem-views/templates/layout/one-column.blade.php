@extends('templates.master')

@section('site-body')
    <div class="container">
        <div class="grid">
            @section('above-content')
                <div class="grid-s-12">
                    @include('partials.breadcrumbs')
                </div>
            @show

            <div class="grid-s-12 s-content">
                @yield('content')
            </div>

            @yield('below-content')
        </div>
    </div>
@endsection




