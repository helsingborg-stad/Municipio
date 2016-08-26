@extends('templates.master')

@section('content')

@if (isset($show_userdata_notice) && $show_userdata_notice)
@include('partials.modal.missing-data')
@endif

<div class="container main-container">

    @include('partials.breadcrumbs')

    @if (isset($show_userdata_notice) && $show_userdata_notice)
    <div class="grid grid-xs-12">
        <div class="notice info">
            <div class="grid no-padding grid-table-md grid-va-middle">
                <div class="grid-fit-content" style="min-width:30px;"><i class="pricon pricon-user-o"></i></div>
                <div class="grid-auto"><strong><?php _e('Your profile is not complete!', 'municipio-intranet'); ?></strong> <?php _e('We\'re missing some information about you.', 'municipio-intranet'); ?></div>
                <div class="grid-fit-content text-right-md text-right-lg">
                    <a href="#modal-missing-info" class="btn btn-primary"><?php _e('Complete your profile', 'municipio-intranet'); ?></a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="grid {{ (wp_get_post_parent_id(get_the_id()) != 0) ? 'no-margin-top' : '' }}">
        @include('partials.sidebar-left')

        <div class="{{ $contentGridSize }} print-grow" id="readspeaker-read">

            @if (is_active_sidebar('content-area-top'))
                <div class="grid sidebar-content-area sidebar-content-area-top">
                    <?php dynamic_sidebar('content-area-top'); ?>
                </div>
            @endif

            @if (is_main_site())
                @if (is_user_logged_in())
                <h1 class="gutter gutter-bottom">{{ \Intranet\User\General::greet() }}</h1>
                @endif
            @else
                @if (have_posts())
                    @while(have_posts())
                        {!! the_post() !!}

                        @include('partials.article')
                    @endwhile
                @endif
            @endif

            @if (is_active_sidebar('content-area'))
                <div class="grid sidebar-content-area sidebar-content-area-bottom">
                    <?php dynamic_sidebar('content-area'); ?>
                </div>
            @endif
        </div>


        @include('partials.sidebar-right')
    </div>
</div>

@stop

