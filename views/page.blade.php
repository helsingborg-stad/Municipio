@extends('views.templates.master')

@section('content')

<div class="container">
    @include('views.partials.breadcrumbs')

    <div class="grid no-margin-top">
        @include('views.partials.sidebar-left')

        <div class="grid-md-8 grid-lg-6">
            @while(have_posts())
                {!! the_post() !!}

                @include('views.partials.article')
            @endwhile

            {!! dynamic_sidebar('content-area') !!}
        </div>

        @include('views.partials.sidebar-right')
    </div>
</div>

@stop
