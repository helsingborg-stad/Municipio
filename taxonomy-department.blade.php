@extends('templates.scaffolding')

@section('content')

<div class="container">
    @include('templates.partials.breadcrumbs')

    <div class="grid no-margin-top">
        <div class="grid-lg-12">
            <h1>{{ $term->name }}</h1>
        </div>
    </div>

    <div class="grid no-margin-top">
        @while(have_posts())
            {{ the_post() }}
            <div class="grid-md-4">
                <a href="{{ the_permalink() }}" class="box box-index">
                    <img class="box-image" src="http://www.helsingborg.se/wp-content/uploads/2015/01/Bibliotek_610x250.jpg">
                    <div class="box-content">
                        <h5 class="box-index-title link-item">{{ the_title() }}</h5>
                        {{ the_excerpt() }}
                    </div>
                </a>
            </div>
        @endwhile
    </div>
</div>

@endsection
