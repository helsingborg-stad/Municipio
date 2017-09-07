<div class="grid-lg-12">
    <a href="{{ get_blog_permalink($item->blog_id, $item->ID) }}" class="{{ $classes }} {{ $item->is_sticky ? 'is-sticky' : '' }}" {!! is_super_admin() ? 'title="Rank: ' . round($item->rank_percent, 3) . '%"' : '' !!}>
        @if (!in_array($args['id'], array('content-area', 'content-area-top')) && $item->thumbnail_image)
            <div class="box-image-container">
                @if ($item->thumbnail_image && $item->image)
                <img class="box-image" src="{{ $i === 1 ? $item->image[0] : $item->thumbnail_image[0] }}" alt="{{ $item->post_title }}">
                @endif
            </div>
        @endif

        <div class="box-content">
            <div class="sub-heading clearfix">
                <span>{!! municipio_intranet_format_site_name(\Intranet\Helper\Multisite::getSite($item->blog_id), 'long') !!}</span>
                <time class="pricon pricon-clock pricon-space-right" datetime="{{ mysql2date('Y-m-d H:i:s', strtotime($item->post_date)) }}">{{ mysql2date(get_option('date_format'), $item->post_date) }}</time>

                <?php switch_to_blog($item->blog_id); ?>
                    @if (comments_open($item->ID) && is_user_logged_in())
                    <span class="comments gutter gutter-right gutter-sm">
                        <span class="pricon pricon-comments pricon-space-right">({{ get_comments_number($item->ID) }})</span>
                    </span>
                    @endif
                <?php restore_current_blog(); ?>

            </div>

            <h3 class="box-title text-highlight">{{ apply_filters('the_title', $item->post_title) }}</h3>

            <article class="clearfix">
                @if (in_array($args['id'], array('content-area', 'content-area-top')) && $item->thumbnail_image)
                    <img src="{{ $item->thumbnail_image[0] }}" alt="{{ $item->post_title }}">
                @endif

                <p>
                    @if(isset(get_extended($item->post_content)['extended']) && !empty(get_extended($item->post_content)['extended']))
                        {!! wp_strip_all_tags(strip_shortcodes(get_extended($item->post_content)['main'])) !!}
                    @else
                        {!! wp_trim_words(wp_strip_all_tags(strip_shortcodes($item->post_content)), 50, '') !!}
                    @endif
                </p>

                <span class="read-more btn-md inline-block"><?php _e('Read more', 'modularity'); ?></span>
            </article>
        </div>
    </a>
</div>
