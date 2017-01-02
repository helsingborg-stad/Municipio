@extends('templates.master')

@section('content')

<section class="creamy creamy-border-bottom gutter-lg gutter-vertical sidebar-content-area archive-filters">
    <div class="container">
        <?php echo do_shortcode('[wp-listings-search-form]'); ?>
    </div>
</section>

<div class="container main-container">
    @include('partials.breadcrumbs')

    <div class="grid">
        @if (get_field('archive_' . sanitize_title($postType) . '_show_sidebar_navigation', 'option'))
            @include('partials.sidebar-left')
        @endif

        <?php
            $cols = 'grid-md-12';
            if (is_active_sidebar('right-sidebar') && get_field('archive_' . sanitize_title($postType) . '_show_sidebar_navigation', 'option')) {
                $cols = 'grid-md-8 grid-lg-6';
            } elseif (is_active_sidebar('right-sidebar') || get_field('archive_' . sanitize_title($postType) . '_show_sidebar_navigation', 'option')) {
                $cols = 'grid-md-12 grid-lg-9';
            }
        ?>

        <div class="{{ $cols }}">

            @if (is_active_sidebar('content-area-top'))
                <div class="grid sidebar-content-area sidebar-content-area-top">
                    <?php dynamic_sidebar('content-area-top'); ?>
                </div>
            @endif

            <div class="grid">
                <div class="grid-xs-12">
                @if (have_posts())
                    <ul>
                    @while(have_posts())
                        {!! the_post() !!}
                        <?php global $post; $thumbnail = municipio_get_thumbnail_source($post->ID, array(500, 500)); ?>

                        <li class="box box-news box-news-horizontal">
                            <a href="{{ the_permalink() }}" class="box-image-container" style="width: 200px;">
                                @if ($thumbnail)
                                    <img src="{{ municipio_get_thumbnail_source(null,array(500,500)) }}" alt="{{ the_title() }}">
                                @else
                                    <figure class="image-placeholder"></figure>
                                @endif
                            </a>
                            <div class="box-content">
                                <div>
                                    <?php
                                        $terms = array();
                                        foreach (get_the_terms(get_the_id(), \WpListings\Listings::$taxonomySlug) as $term) {
                                            $terms[] = $term->name;
                                        }
                                    ?>
                                    {{ implode(', ', (array) $terms) }},
                                    {{ isset(get_the_terms(get_the_id(), \WpListings\Listings::$taxonomySlug)[0]) ? get_the_terms(get_the_id(), \WpListings\Listings::$placesTaxonomySlug)[0]->name : null }}
                                </div>
                                <h2 class="box-title text-highlight"><a href="{{ the_permalink() }}">{{ the_title() }}</a></h2>
                                <p class="text-xl" style="margin-top: 0;">{{ municiipio_format_currency(get_post_meta(get_the_id(), 'listing_price', true)) }}{{ apply_filters('wp-listings/currency', ':-') }}</p>
                            </div>
                        </li>
                    @endwhile
                    </ul>
                @else
                    <div class="notice info pricon pricon-info-o pricon-space-right"><?php _e('No posts to show'); ?>…</div>
                @endif
                </div>
            </div>

            @if (is_active_sidebar('content-area'))
                <div class="grid sidebar-content-area sidebar-content-area-bottom">
                    <?php dynamic_sidebar('content-area'); ?>
                </div>
            @endif

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

        @include('partials.sidebar-right')
    </div>
</div>

@stop
