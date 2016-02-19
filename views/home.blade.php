@extends('templates.master')

@section('content')

<div class="container">
    @include('partials.breadcrumbs')

    <div class="grid no-margin-top">
        <div class="grid-md-8 grid-lg-8">
            <div class="grid">
                <div class="grid-sm-12">
                    @while(have_posts())
                        {!! the_post() !!}

                        @include('partials.post')
                    @endwhile
                </div>
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
