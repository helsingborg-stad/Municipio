<?php global $post; $post = $item; setup_postdata($post); ?>
<li>
    <div class="search-result-item">
        <span class="search-result-date">{{ apply_filters('Municipio/search_result/date', get_the_modified_date(), get_post()) }}</span><br>

        @if (get_post_type() === 'attachment')
            <h3><a href="{{ apply_filters('Municipio/search_result/permalink_url', get_blog_permalink($item->blog_id, $item->ID), get_post()) }}" class="{{ municipio_get_mime_link_item($post->post_mime_type) }}">{{ apply_filters('Municipio/search_result/title', get_the_title() ? get_the_title() : __('Unknown media', 'municipio-intranet'), get_post()) }}</a></h3>
            <span class="network-title label label-sm label-creamy">{!! municipio_intranet_format_site_name(\Intranet\Helper\Multisite::getSite($item->blog_id)) !!}</span>
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
