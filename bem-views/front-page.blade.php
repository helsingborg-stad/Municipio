@extends('templates.master')

@section('main')
    @if (is_active_sidebar('content-area'))
    <section class="creamy creamy-border-bottom gutter-xl gutter-vertical sidebar-content-area s-content-area">
        <div class="container">
            <div class="grid">
                <?php dynamic_sidebar('content-area'); ?>
            </div>
        </div>
    </section>
    @endif

    @if (is_active_sidebar('content-area-bottom'))
    <div class="container gutter-xl gutter-vertical sidebar-content-area-bottom">
        <div class="grid">
            <?php dynamic_sidebar('content-area-bottom'); ?>
        </div>
    </div>
    @endif
@endsection
