@extends($wp_parent_theme . '.views.templates.master')

@section('content')

<div class="container">
    @include($wp_parent_theme . '.views.partials.breadcrumbs')

    <div class="grid no-margin-top">
        @include($wp_parent_theme . '.views.partials.sidebar-left')

        <div class="grid-md-8 grid-lg-6">
            @while(have_posts())
                {!! the_post() !!}

                @include($wp_parent_theme . '.views.partials.article')
            @endwhile

            {!! dynamic_sidebar('content-area') !!}
        </div>

        @include($wp_parent_theme . '.views.partials.sidebar-right')
    </div>
</div>

@stop
