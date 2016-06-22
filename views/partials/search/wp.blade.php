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
    <div class="container">
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
                        <ul class="search-result-list">

                            @while(have_posts())
                                {!! the_post() !!}
                                <li>
                                    <div class="search-result-item">
                                        <span class="search-result-date">{{ apply_filters('Municipio/search_result/date', get_the_modified_date(), get_post()) }}</span>
                                        <h3><a class="link-item" href="{{ apply_filters('Municipio/search_result/permalink_url', get_permalink(), get_post()) }}">{{ apply_filters('Municipio/search_result/title', get_the_title(), get_post()) }}</a></h3>
                                        <p>{{ apply_filters('Municipio/search_result/excerpt', get_the_excerpt(), get_post()) }}</p>
                                        <div class="search-result-info">
                                            <span class="search-result-url"><i class="fa fa-globe"></i> <a href="{{ apply_filters('Municipio/search_result/permalink_url', get_permalink(), get_post()) }}">{{ apply_filters('Municipio/search_result/permalink_text', get_permalink(), get_post()) }}</a></span>
                                        </div>
                                    </div>
                                </li>
                            @endwhile

                        </ul>
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
