@extends('templates.master')

@section('content')

@if (is_active_sidebar('content-area'))
<section class="creamy creamy-border-bottom  u-py-5 sidebar-content-area ">
    <div class="container">
        <div class="grid grid--columns">
            <?php dynamic_sidebar('content-area'); ?>
        </div>
    </div>
</section>
@endif

@stop
