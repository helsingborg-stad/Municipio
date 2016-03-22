@extends('templates.master')

@section('content')

<div class="container main-container">
    @include('partials.breadcrumbs')

    <div class="grid">
        <div class="grid-md-8 grid-lg-8">
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

        @include('partials.sidebar-right')
    </div>
</div>

@stop
