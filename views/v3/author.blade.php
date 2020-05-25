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
             _e('Posts by', 'municipio'); ?> {{ municipio_get_author_full_name() ?
                municipio_get_author_full_name() : get_the_author_meta('nicename') }}
        @endtypography


        @if (have_posts())
            @while(have_posts())
                {!! the_post() !!}

                @if (in_array($template, array('full', 'compressed', 'collapsed', 'horizontal-cards')))

                    @include('partials.post.post-' . $template)
                @else
                    @include('partials.post.post-' . $template)
                @endif
            @endwhile
        @else
            <div class="grid-xs-12">
                <?php _e('No posts to show'); ?>
                …
            </div>
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



    @include('partials.sidebar-right')


@stop