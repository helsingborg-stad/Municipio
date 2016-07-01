@extends('templates.master')

@section('content')

<?php global $wp_query; ?>

<section class="creamy creamy-border-bottom gutter-vertical gutter-lg clearfix">
    <div class="container">
        <div class="gid">
            <div class="grid-lg-12">
                {!! get_search_form() !!}

                <div class="gutter gutter-sm gutter-top">
                    <strong>{{ $resultCount }}</strong> träffar på <strong>"{{ get_search_query() }}"</strong>
                </div>
            </div>
        </div>
    </div>
</section>

@if ($resultCount === 0)

<div class="container gutter gutter-lg gutter-top">
    <div class="grid gutter gutter-lg gutter-top">
        @include('partials.sidebar-left')

        <div class="{{ $contentGridSize }}">
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
            @include('partials.sidebar-left')

            <div class="{{ $contentGridSize }}">
                @if ($wp_query->max_num_pages > 1)
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
                                @if (isset($item->user_login))
                                <?php global $authordata; $authordata = get_user_by('ID', $item->ID); ?>
                                <li>
                                    <div class="search-result-item">
                                        <div class="search-result-item-user">
                                            <div class="profile-header-background" style="background-image:url('{{ !empty(get_the_author_meta('user_profile_picture')) ? get_the_author_meta('user_profile_picture') : 'http://www.helsingborg.se/wp-content/uploads/2016/05/varen_2016_2_1800x350.jpg' }}');"></div>

                                            @if (!empty(get_the_author_meta('user_profile_picture')))
                                            <div class="profile-image" style="background-image:url('{{ get_the_author_meta('user_profile_picture') }}');"></div>
                                            @endif

                                            <div class="profile-basics">
                                                <h3><a href="{{ municipio_intranet_get_user_profile_url($item->ID) }}">{{ municipio_intranet_get_user_full_name(get_the_author_meta('ID')) }}</a></h3>

                                                @if (!empty(get_the_author_meta('ad_title')))
                                                     <span class="profile-title">{{ get_the_author_meta('ad_title') }}</span>
                                                @elseif (!empty(get_the_author_meta('user_work_title')))
                                                    <span class="profile-title">{{ get_the_author_meta('user_work_title') }}</span>
                                                @endif

                                                @if (!empty(get_the_author_meta('user_administration_unit')) || !empty(get_the_author_meta('user_department')))
                                                    <span class="profile-department">
                                                        {{ !empty(get_the_author_meta('user_administration_unit')) ? municipio_intranet_get_administration_unit_name(get_the_author_meta('user_administration_unit')) : '' }}{{ !empty(get_the_author_meta('user_administration_unit')) && !empty(get_the_author_meta('user_department')) ? ',' : '' }}
                                                        {{ !empty(get_the_author_meta('user_department')) ? get_the_author_meta('user_department') : '' }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="search-result-info">
                                            <span class="search-result-url"><i class="fa fa-user"></i> <a href="{{ municipio_intranet_get_user_profile_url($item->ID) }}">{{ municipio_intranet_get_user_profile_url($item->ID) }}</a></span>
                                        </div>
                                    </div>
                                </li>
                                @else
                                <?php global $post; $post = $item; setup_postdata($post); ?>
                                <li>
                                    <div class="search-result-item">
                                        <span class="search-result-date">{{ apply_filters('Municipio/search_result/date', get_the_modified_date(), get_post()) }}</span><br>

                                        @if (get_post_type() === 'attachment')
                                            <h3><a href="{{ apply_filters('Municipio/search_result/permalink_url', get_blog_permalink($item->blog_id, $item->ID), get_post()) }}" class="{{ municipio_get_mime_link_item($post->post_mime_type) }}">{{ apply_filters('Municipio/search_result/title', get_the_title() ? get_the_title() : __('Unknown media', 'municipio-intranet'), get_post()) }}</a></h3>
                                        @else
                                            <h3><a href="{{ apply_filters('Municipio/search_result/permalink_url', get_blog_permalink($item->blog_id, $item->ID), get_post()) }}">{{ apply_filters('Municipio/search_result/title', get_the_title(), get_post()) }}</a></h3>
                                            <span class="network-title label label-sm label-creamy">{!! municipio_intranet_format_site_name(\Intranet\Helper\Multisite::getSite($item->blog_id)) !!}</span>
                                            <p>{!! apply_filters('Municipio/search_result/excerpt', get_the_excerpt(), get_post()) !!}</p>
                                        @endif

                                        <div class="search-result-info">
                                            <span class="search-result-url"><i class="fa fa-globe"></i> <a href="{{ apply_filters('Municipio/search_result/permalink_url', get_blog_permalink($item->blog_id, $item->ID), get_post()) }}">{{ apply_filters('Municipio/search_result/permalink_text', get_blog_permalink($item->blog_id, $item->ID), get_post()) }}</a></span>
                                        </div>
                                    </div>
                                </li>
                                @endif
                            @endforeach

                        </ul>
                    </div>
                </div>

                @if ($wp_query->max_num_pages > 1)
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
