@extends('templates.layout.one-column')

@section('content')
    @if (have_posts())
        <div class="grid" @if (in_array($template, array('cards'))) data-equal-container @endif>
            <?php $postNum = 0; ?>
            @while(have_posts())
                {!! the_post() !!}

                @if (in_array($template, array('full', 'compressed', 'collapsed', 'horizontal-cards')))
                    <div class="grid-xs-12 post">
                        @include('partials.blog.type.post-' . $template)
                    </div>
                @else
                    @include('partials.blog.type.post-' . $template)
                @endif

                <?php $postNum++; ?>
            @endwhile
        </div>
    @else
        <div class="notice info pricon pricon-info-o pricon-space-right"><?php _e('No posts to show', 'municipio'); ?>…</div>
    @endif
@stop
