@extends('views.templates.scaffolding')

@section('content')

<div class="container">
    @include('views.partials.breadcrumbs')

    <div class="grid no-margin-top">
        @include('views.partials.sidebar-left')

        @while(have_posts())
            {{ the_post() }}

            @include('views.partials.article')
        @endwhile

        @include('views.partials.sidebar-right')
    </div>
</div>

@stop
