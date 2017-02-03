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

                                    @if (wp_listings_use_price())
                                    <footer class="post-footer gutter gutter-bottom">
                                        <label class="inline-block"><?php _e('Price', 'municipio'); ?>:</label>
                                        <span class="text-xl inline-block">
                                            {{ municiipio_format_currency(get_post_meta(get_the_id(), 'listing_price', true)) }}{{ apply_filters('wp-listings/currency', ':-') }}
                                        </span>
                                    </footer>
                                    @endif

                                    <footer class="listing-actions gutter gutter-bottom gutter-sm">
                                        {!! wp_listings_delete_listing_button() !!}
                                    </footer>

                                    @if(!empty(wp_listings_get_documents()))
                                        <div class="box box-panel box-panel-secondary">
                                            <h4 class="box-title"><?php _e('Attached files', 'municipio'); ?></h4>
                                            <ul>
                                                @foreach (wp_listings_get_documents() as $field)
                                                    <li><a href="{{ wp_get_attachment_url($field['document_file']) }}" class="link-item link-item-outbound">{{ $field['document_title'] }}</a></li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if (wp_listings_get_meta_fields())
                                    <div class="gutter gutter-vertical gutter-lg">
                                        <table class="table table-bordered table-striped table-sm">
                                            <thead>
                                                <tr>
                                                @foreach (wp_listings_get_meta_fields() as $field)
                                                    <td>{{ $field['label'] }}</td>
                                                @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                @foreach (wp_listings_get_meta_fields() as $field)
                                                    <td>{{ get_post_meta($post->ID, $field['name'], true) ? get_post_meta($post->ID, $field['name'], true) : '-' }}</td>
                                                @endforeach
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    @endif
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

