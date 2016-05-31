@extends('templates.master')

@section('content')

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
                @if ($resultCount > 0)
                <div class="grid">
                    <div class="grid-lg-12">
                        {!! $pagination !!}
                    </div>
                </div>
                @endif

                <div class="grid">
                    <div class="grid-lg-12">
                        <ul class="search-result-list">

                            @foreach ($results as $item)
                                <?php global $post; $post = $item; setup_postdata($post); ?>
                                <li>
                                    <div class="search-result-item">
                                        <span class="search-result-date">{{ apply_filters('Municipio/search_result/date', get_the_modified_date(), get_post()) }}</span>
                                        <h3><a href="{{ apply_filters('Municipio/search_result/permalink_url', get_blog_permalink($item->blog_id, $item->ID), get_post()) }}">{{ apply_filters('Municipio/search_result/title', get_the_title(), get_post()) }}</a></h3>
                                        <span class="network-title label label-sm label-creamy">{!! municipio_intranet_format_site_name(\Intranet\Helper\Multisite::getSite($item->blog_id)) !!}</span>
                                        <p>{{ apply_filters('Municipio/search_result/excerpt', get_the_excerpt(), get_post()) }}</p>
                                        <div class="search-result-info">
                                            <span class="search-result-url"><i class="fa fa-globe"></i> <a href="{{ apply_filters('Municipio/search_result/permalink_url', get_blog_permalink($item->blog_id, $item->ID), get_post()) }}">{{ apply_filters('Municipio/search_result/permalink_text', get_blog_permalink($item->blog_id, $item->ID), get_post()) }}</a></span>
                                        </div>
                                    </div>
                                </li>
                            @endforeach

                        </ul>
                    </div>
                </div>

                @if ($resultCount > 0)
                <div class="grid">
                    <div class="grid-lg-12">
                        {!! $pagination !!}
                    </div>
                </div>
                @endif
            </div>

            @include('partials.sidebar-right')
        </div>
    </div>
</section>

@endif
@stop
