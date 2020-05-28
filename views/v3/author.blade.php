@extends('templates.master')

@section('content')

    @include('partials.archive.archive-filters')

    @if (is_active_sidebar('content-area-top'))
        @includeIf('partials.sidebar', ['id' => 'content-area-top'])
    @endif

    <div class="grid">

        @typography([
            "element" => "h2"
        ])
            {{_e('Posts by', 'municipio')}}
            {{municipio_get_author_full_name() ? municipio_get_author_full_name() : get_the_author_meta('nicename') }}
        @endtypography

        @if (in_array($template, array('cards', 'compressed', 'grid', 'list', 'newsitem')))
            @include('partials.post.post-' . $template)
        @else
            @include('partials.post.post-list')
        @endif

    </div>


    @if (is_active_sidebar('content-area'))
        <?php dynamic_sidebar('content-area'); ?>
    @endif


    {!!
        paginate_links(array(
            'type' => 'list'
        ))
    !!}



    @if (is_active_sidebar('sidebar-right'))
        @includeIf('partials.sidebar', ['id' => 'sidebar-right'])
    @endif


@stop

<?php #phpinfo(); ?>