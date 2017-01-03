@extends('templates.master')

@section('content')

<div class="container main-container">
    @include('partials.breadcrumbs')

    <div class="grid">
        <div class="grid-md-12 grid-lg-9">
            @if (is_single() && is_active_sidebar('content-area-top'))
                <div class="grid sidebar-content-area sidebar-content-area-top">
                    <?php dynamic_sidebar('content-area-top'); ?>
                </div>
            @endif

            <div class="grid">
                <div class="grid-sm-12">
                        {!! the_post() !!}

                        <?php global $post; ?>
                        <div class="grid">
                            <div class="grid-xs-12">
                                <div class="post post-single">
                                    @include('partials.blog.post-header')

                                    <?php if (get_field('listings_images')) : ?>
                                    <div class="slider slider-nav-bottom" style="height: 450px;max-height: 450px;">
                                        <ul>
                                            <?php foreach (get_field('listings_images') as $image) : ?>
                                            <li><div class="slider-image" style="background-image:url('<?php echo $image; ?>');"></div></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    <?php endif; ?>

                                    <article id="article">
                                        {{ the_content() }}
                                    </article>

                                    <footer class="post-footer gutter gutter-bottom">
                                        <label class="inline-block"><?php _e('Price', 'municipio'); ?>:</label>
                                        <span class="text-xl inline-block">
                                            {{ municiipio_format_currency(get_post_meta(get_the_id(), 'listing_price', true)) }}{{ apply_filters('wp-listings/currency', ':-') }}
                                        </span>
                                    </footer>
                                </div>
                            </div>
                        </div>

                        @include('partials.blog.post-footer')

                        <div class="grid">
                            <div class="grid-md-12">
                                <?php echo do_shortcode('[wp-listings-contact-form]'); ?>
                            </div>
                        </div>
                </div>
            </div>
        </div>

        @include('partials.sidebar-right')
    </div>
</div>

@stop

