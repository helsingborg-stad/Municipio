@extends('templates.master')

@section('content')

@if (is_active_sidebar('content-area'))
<section class="creamy creamy-border-bottom gutter-xl gutter-vertical">
    <div class="container">
        <div class="grid">
            {!! dynamic_sidebar('content-area') !!}
        </div>
    </div>
</section>
@endif

@if (is_active_sidebar('content-area-bottom'))
<div class="container gutter-xl gutter-vertical">
    <div class="grid">
        {!! dynamic_sidebar('content-area-bottom') !!}
    </div>
</div>
@endif

@if (!is_active_sidebar('content-area') && !is_active_sidebar('content-area-bottom') && current_user_can('edit_posts'))
<section class="gutter-xl gutter-vertical">
<div class="notice warning text-center">
     <i class="fa fa-warning"></i> {{ __('There\'s no active modules on this page. Please add modules to your front page.') }}
</div>
</section>
@endif

@stop
