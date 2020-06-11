@extends('templates.master')
@section('content')

<div class="container main-container u-pb-5">

    @include('partials.breadcrumbs')

    <div class="grid {{ implode(' ', apply_filters('Municipio/Page/MainGrid/Classes', wp_get_post_parent_id(get_the_id()) != 0 ? array('no-margin-top') : array())) }}">

        <div class="grid-xs-12 grid-md-6 grid-print-12" id="readspeaker-read">

            @if (is_active_sidebar('content-area-top'))
                <div class="grid grid--columns sidebar-content-area sidebar-content-area-top">
                    <?php dynamic_sidebar('content-area-top'); ?>
                </div>
            @endif

            @while(have_posts())
                {!! the_post() !!}

                @include('partials.article')
            @endwhile

            @if (is_active_sidebar('content-area'))
                <div class="grid grid--columns sidebar-content-area sidebar-content-area-bottom">
                    <?php dynamic_sidebar('content-area'); ?>
                </div>
            @endif

            <div class="hidden-xs hidden-sm hidden-print">
                @include('partials.page-footer')
            </div>
        </div>


        @if ($hasRightSidebar)
            <aside class="grid-xs-12 grid-md-6 sidebar-right-sidebar">
                @if (is_active_sidebar('right-sidebar') || (isset($enabledSidebarFilters) && is_array($enabledSidebarFilters)))
                <div class="grid grid--columns">

                    @if (isset($enabledSidebarFilters) && is_array($enabledSidebarFilters))
                        @include('partials.blog.taxonomy-filters')
                    @endif

                    <?php dynamic_sidebar('right-sidebar'); ?>
                </div>
                @endif
            </aside>
        @endif
    </div>

    <div class="grid grid--columns hidden-md hidden-lg u-hidden@xl">
        <div class="grid-md-8 offset-md-4">
            @include('partials.page-footer')
        </div>
    </div>
</div>

@stop
