@extends('templates.master')

@section('content')

<div class="container">
    @include('partials.breadcrumbs')

    <div class="grid no-margin-top">
        <div class="grid-md-8 grid-lg-8">
            <div class="grid">
                @while(have_posts())
                    {!! the_post() !!}

                    @include('partials.post')
                @endwhile
            </div>

            @if (is_active_sidebar('content-area'))
                <div class="grid">
                    {!! dynamic_sidebar('content-area') !!}
                </div>
            @endif
        </div>

        @include('partials.sidebar-right')
    </div>
</div>

@stop
