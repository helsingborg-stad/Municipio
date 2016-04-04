@extends('templates.master')

@section('content')

<div class="container main-container">
    @include('partials.breadcrumbs')

    <div class="grid">
        <div class="{{ is_active_sidebar('right-sidebar') ? 'grid-md-9' : 'grid-md-12' }}">
            <div class="grid">
                @while(have_posts())
                    {!! the_post() !!}

                    @if ($template == 'full')
                        @include('partials.blog.type.post')
                    @else
                        @include('partials.blog.type.post-' . $template)
                    @endif
                @endwhile
            </div>

            <div class="grid">
                <div class="grid-sm-12 text-center">
                    {!!
                        paginate_links(array(
                            'type' => 'list'
                        ))
                    !!}
                </div>
            </div>
        </div>

        @if (is_active_sidebar('right-sidebar'))
        @include('partials.sidebar-right')
        @endif
    </div>
</div>

@stop
