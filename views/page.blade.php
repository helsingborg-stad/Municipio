@extends('templates.master')

@section('content')

<div class="container">
    @include('partials.breadcrumbs')

    <div class="grid no-margin-top">
        @include('partials.sidebar-left')

        <div class="grid-md-8 grid-lg-6">
            @while(have_posts())
                {!! the_post() !!}

                @include('partials.article')
            @endwhile

            {!! dynamic_sidebar('content-area') !!}
        </div>

        @include('partials.sidebar-right')
    </div>
</div>

@stop
