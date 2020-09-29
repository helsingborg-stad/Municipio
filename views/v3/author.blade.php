@extends('templates.archive')

@section('content')

    @includeIf('partials.sidebar', ['id' => 'content-area-top', 'classes' => ['o-grid']])

    @include('partials.archive.archive-filters')

    <div class="author-archive">

        @typography([
            "element" => "h2"
        ])
            {{_e('Posts by', 'municipio')}}
            {{municipio_get_author_full_name() ? municipio_get_author_full_name() : get_the_author_meta('nicename') }}
        @endtypography

        @if (in_array($template, array('cards', 'compressed', 'list', 'newsitem')))
            @include('partials.post.post-' . $template)
        @else
            @include('partials.post.post-list')
        @endif

    </div>

    @includeIf('partials.sidebar.default', ['id' => 'content-area', 'classes' => ['o-grid']])

    @pagination([
        'list' => $paginationList,
        'classList' => ['u-margin__top--4'],
        'current' => isset($_GET['paged']) ? $_GET['paged'] : 1,
        'linkPrefix' => '?paged='
    ])
    @endpagination
@stop