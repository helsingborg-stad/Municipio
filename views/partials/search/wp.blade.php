<?php global $wp_query; ?>

<section class="creamy gutter-vertical gutter-lg clearfix">
    <div class="container">
        <div class="gid">
            <div class="grid-lg-12">
                {!! get_search_form() !!}
                <div class="gutter gutter-sm gutter-top">
                    <strong>{{ $resultCount }}</strong> träffar på <strong>"{{ get_search_query() }}"</strong> inom {{ ucfirst(explode('//', home_url(), 2)[1]) }}
                </div>
            </div>
        </div>
    </div>
</section>

<?php do_action('search_notices'); ?>

@if ($resultCount === 0)

<div class="container gutter gutter-lg gutter-top">
    <div class="grid gutter gutter-lg gutter-top">
        <div class="grid-lg-12">
            <div class="notice info">
                <i class="fa fa-info-circle"></i> <?php _e('Found no matching results on your search…', 'municipio'); ?>
            </div>
        </div>
    </div>
</div>

@else

<section>
    <div class="container main-container">
        <div class="grid">
            <div class="grid-md-12 grid-lg-9">
                @if ($wp_query->max_num_pages > 1)
                <div class="grid">
                    <div class="grid-lg-12">
                        {!!
                            paginate_links(array(
                                'type' => 'list'
                            ))
                        !!}
                    </div>
                </div>
                @endif

                <div class="grid">
                    <div class="grid-lg-12">
                        @if ($template === 'grid')
                            <div class="grid">
                                @while(have_posts())
                                    {!! the_post() !!}
                                        <?php
                                        $date = apply_filters('Municipio/search_result/date', get_the_modified_date(), get_post());
                                        $permalink = apply_filters('Municipio/search_result/permalink_url', get_permalink(), get_post());
                                        $permalinkText = apply_filters('Municipio/search_result/permalink_text', get_permalink(), get_post());
                                        $title = apply_filters('Municipio/search_result/title', get_the_title(), get_post());
                                        $lead = apply_filters('Municipio/search_result/excerpt', get_the_excerpt(), get_post());
                                        $thumbnail = wp_get_attachment_image_src(
                                            get_post_thumbnail_id(),
                                            apply_filters('Modularity/index/image', municipio_to_aspect_ratio('16:9', array(500, 500))                                            )
                                        );

                                        if (is_array($thumbnail)) {
                                            $thumbnail = $thumbnail[0];
                                        }
                                        ?>
                                        @include('partials.search.result-item-grid')
                                @endwhile
                            </div>
                        @else
                            <ul class="search-result-list">
                                @while(have_posts())
                                    {!! the_post() !!}
                                    <li>
                                        <?php
                                        $date = apply_filters('Municipio/search_result/date', get_the_modified_date(), get_post());
                                        $permalink = apply_filters('Municipio/search_result/permalink_url', get_permalink(), get_post());
                                        $permalinkText = apply_filters('Municipio/search_result/permalink_text', get_permalink(), get_post());
                                        $title = apply_filters('Municipio/search_result/title', get_the_title(), get_post());
                                        $lead = apply_filters('Municipio/search_result/excerpt', get_the_excerpt(), get_post());
                                        $thumbnail = wp_get_attachment_image_src(
                                            get_post_thumbnail_id(),
                                            apply_filters('Modularity/index/image', municipio_to_aspect_ratio('16:9', array(200, 200))                                            )
                                        );

                                        if (is_array($thumbnail)) {
                                            $thumbnail = $thumbnail[0];
                                        }
                                        ?>
                                        @include('partials.search.result-item')
                                    </li>
                                @endwhile
                            </ul>
                        @endif
                    </div>
                </div>

                @if ($wp_query->max_num_pages > 1)
                <div class="grid">
                    <div class="grid-lg-12">
                        {!!
                            paginate_links(array(
                                'type' => 'list'
                            ))
                        !!}
                    </div>
                </div>
                @endif
            </div>

            @include('partials.sidebar-right')
        </div>
    </div>
</section>

@endif
