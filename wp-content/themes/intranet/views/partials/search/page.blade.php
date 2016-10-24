<ul class="search-result-list grid">
    @while(have_posts())
    {!! the_post() !!}
    <?php global $post; ?>
    <li>
        <div class="search-result-item">
            <span class="network-title label label-sm label-purple-5">{!! municipio_intranet_format_site_name(\Intranet\Helper\Multisite::getSite($post->site_id), 'long') !!}</span>

            @if (get_post_type() === 'attachment')
                <h3><a href="{{ apply_filters('Municipio/search_result/permalink_url', get_blog_permalink($post->site_id, $post->ID), get_post()) }}" class="{{ municipio_get_mime_link_item($post->post_mime_type) }}">{{ apply_filters('Municipio/search_result/title', get_the_title() ? get_the_title() : __('Unknown media', 'municipio-intranet'), get_post()) }}</a></h3>
            @else
                <h3><a href="{{ apply_filters('Municipio/search_result/permalink_url', get_blog_permalink($post->site_id, $post->ID), get_post()) }}">{{ apply_filters('Municipio/search_result/title', get_the_title(), get_post()) }}</a></h3>
                <p>{!! apply_filters('Municipio/search_result/excerpt', get_the_excerpt(), get_post()) !!}</p>
            @endif

            <div class="search-result-info">
                <span class="search-result-url"><i class="fa fa-globe"></i> <a href="{{ apply_filters('Municipio/search_result/permalink_url', get_blog_permalink($item->blog_id, $item->ID), get_post()) }}">{{ apply_filters('Municipio/search_result/permalink_text', get_blog_permalink($item->blog_id, $item->ID), get_post()) }}</a></span>
            </div>
        </div>
    </li>
    @endwhile
</ul>
