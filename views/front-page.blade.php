@extends('templates.master')

@section('content')

@if (is_active_sidebar('content-area'))
<section class="creamy creamy-border-bottom gutter-xl gutter-vertical sidebar-content-area">
    <div class="container">
        <div class="grid">
            <?php dynamic_sidebar('content-area'); ?>
        </div>
    </div>
</section>
@endif

@if (!is_active_sidebar('content-area') && !is_active_sidebar('content-area-bottom') && current_user_can('edit_posts'))
<section class="gutter-xl gutter-vertical">
<div class="notice warning text-center">
     <i class="fa fa-warning"></i> {{ __('There\'s no active modules on this page. Please add modules to your front page.') }}
</div>
</section>
@endif

@stop
