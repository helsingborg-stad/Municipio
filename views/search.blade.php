@extends('views.templates.master')

@section('content')

<section class="creamy gutter-vertical gutter-lg clearfix">
    <div class="container">
        <div class="gid">
            <div class="grid-lg-12">
                <?php get_search_form(); ?>
            </div>
        </div>
    </div>
</section>

@stop
