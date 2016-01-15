@extends('templates.scaffolding')

@section('content')

<div class="container">
    @include('templates.partials.breadcrumbs')

    <div class="grid no-margin-top">
        @include('templates.partials.sidebar-left')

        @while(have_posts())
            {{ the_post() }}

            <article class="grid-md-8 grid-lg-6">
                <h1>{{ the_title() }}</h1>
                {{ the_content() }}
            </article>
        @endwhile

        @include('templates.partials.sidebar-right')
    </div>
</div>

@stop
