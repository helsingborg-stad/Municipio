@extends('templates.master')

@section('content')

<section class="creamy creamy-border-bottom gutter-lg gutter-vertical sidebar-content-area">
    <form method="get" action="" class="container" id="archive-filter">
        <div class="grid">
            <div class="grid-md-5">
                <label for="filter-keyword" class="text-sm"><strong><?php _e('Title', 'municipio'); ?>:</strong></label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-search"></i></span>
                    <input type="text" name="s" id="filter-keyword" class="form-control" value="{{ isset($_GET['s']) && !empty($_GET['s']) ? $_GET['s'] : '' }}">
                </div>
            </div>
            <div class="grid-md-5">
                <label for="filter-date-from" class="text-sm"><strong><?php _e('Date published', 'municipio'); ?>:</strong></label>
                <div class="input-group">
                    <span class="input-group-addon"><?php _e('From', 'municipio'); ?>:</span>
                    <input type="text" name="from" placeholder="<?php _e('From date', 'municipio'); ?>…" id="filter-date-from" class="form-control datepicker-range datepicker-range-from" value="{{ isset($_GET['from']) && !empty($_GET['from']) ? $_GET['from'] : '' }}" readonly>
                    <span class="input-group-addon"><?php _e('To', 'municipio'); ?>:</span>
                    <input type="text" name="to" placeholder="<?php _e('To date', 'municipio'); ?>" class="form-control datepicker-range datepicker-range-to" value="{{ isset($_GET['to']) && !empty($_GET['to']) ? $_GET['to'] : '' }}" readonly>
                </div>
            </div>
            <div class="grid-md-2">
                 <label for="filter-date-from" class="text-sm">&nbsp;</label>
                <input type="submit" value="<?php _e('Filter', 'municipio'); ?>" class="btn btn-primary btn-block">
            </div>
        </div>
    </form>
</section>

<div class="container main-container">
    @include('partials.breadcrumbs')

    <div class="grid">
        <div class="{{ is_active_sidebar('right-sidebar') ? 'grid-md-12 grid-lg-9' : 'grid-md-12' }}">
            <div class="grid" data-equalize-container>
                @if (have_posts())
                    @while(have_posts())
                        {!! the_post() !!}

                        @if (get_field('blog_feed_post_style', 'option') == 'full' || !get_field('blog_feed_post_style', 'option'))
                            @include('partials.blog.type.post')
                        @elseif(get_field('blog_feed_post_style', 'option') == 'collapsed')
                            @include('partials.blog.type.post-collapsed')
                        @elseif(get_field('blog_feed_post_style', 'option') == 'compressed')
                            @include('partials.blog.type.post-compressed')
                        @elseif(get_field('blog_feed_post_style', 'option') == 'grid')
                            @include('partials.blog.type.post-grid')
                        @elseif(get_field('blog_feed_post_style', 'option') == 'cards')
                            @include('partials.blog.type.post-cards')
                        @endif
                    @endwhile
                @else
                    <div class="grid-sm-12"><i class="fa fa-frown-o"></i> <?php _e('No posts found…', 'municipio'); ?></div>
                @endif
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

        @if (is_active_sidebar('right-sidebar'))
        @include('partials.sidebar-right')
        @endif
    </div>
</div>

@stop
