<div class="grid-lg-12">
    <a <?php if (is_super_admin()) : ?>title="Rank: <?php echo round($item->rank_percent, 3); ?>%"<?php endif; ?> href="<?php echo get_blog_permalink($item->blog_id, $item->ID); ?>" class="<?php echo implode(' ', apply_filters('Modularity/Module/Classes', array('box', 'box-news', 'box-news-horizontal'), $module->post_type, $args)); ?> <?php echo $item->is_sticky ? 'is-sticky' : ''; ?>">
        <?php if (!in_array($args['id'], array('content-area', 'content-area-top')) && $item->thumbnail_image) : ?>
            <div class="box-image-container">
                <?php if ($item->thumbnail_image && $item->image) : ?>
                <img class="box-image" src="<?php echo $i === 1 ? $item->image[0] : $item->thumbnail_image[0]; ?>" alt="<?php echo $item->post_title; ?>">
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="box-content">
            <div class="sub-heading clearfix">
                <span><?php echo municipio_intranet_format_site_name(\Intranet\Helper\Multisite::getSite($item->blog_id), 'long'); ?></span>
                <time class="pricon pricon-clock pricon-space-right" datetime="<?php echo mysql2date('Y-m-d H:i:s', strtotime($item->post_date)); ?>"><?php echo mysql2date(get_option('date_format'), $item->post_date); ?></time>
            </div>
            <h3 class="box-title text-highlight"><?php echo apply_filters('the_title', $item->post_title); ?></h3>

            <article class="clearfix">
                <?php if (in_array($args['id'], array('content-area', 'content-area-top')) && $item->thumbnail_image) : ?>
                    <img src="<?php echo $item->thumbnail_image[0]; ?>" alt="<?php echo $item->post_title; ?>">
                <?php endif; ?>

                <p>
                    <?php
                    if (isset(get_extended($item->post_content)['extended']) && !empty(get_extended($item->post_content)['extended'])) {
                        echo wp_strip_all_tags(get_extended($item->post_content)['main']);
                    } else {
                        echo wp_trim_words($item->post_content, 50, '');
                    }
                    ?>
                </p>

                <span class="read-more btn-md inline-block"><?php _e('Read more', 'modularity'); ?></span>
            </article>
        </div>
    </a>
</div>
