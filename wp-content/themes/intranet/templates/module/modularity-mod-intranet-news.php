<?php
    $news = false;
    $display = get_field('display', $module->ID);
    $limit = !empty(get_field('limit', $module->ID)) ? get_field('limit', $module->ID) : 10;

    switch ($display) {
        default:
        case 'network_subscribed':
            if (is_user_logged_in()) {
                $subscriptions = array_merge(
                    \Intranet\User\Subscription::getForcedSubscriptions(true),
                    \Intranet\User\Subscription::getSubscriptions(null, true)
                );

                $news = \Intranet\CustomPostType\News::getNews($limit, $subscriptions);
            } else {
                $news = \Intranet\CustomPostType\News::getNews($limit, 'all');
            }
            break;

        case 'network':
            $news = \Intranet\CustomPostType\News::getNews($limit, 'all');
            break;

        case 'blog':
            $news = \Intranet\CustomPostType\News::getNews($limit, array(get_current_blog_id()));
            break;
    }

    if (count($news) > 0) :

    $hasImages = false;

    foreach ($news as $item) {
        if (get_thumbnail_source($item->ID) !== false) {
            $hasImages = true;
        }
    }
?>
<div class="grid">
    <?php foreach ($news as $item) : ?>
        <?php
            $thumbnail_image = wp_get_attachment_image_src(
                get_post_thumbnail_id($item->ID),
                apply_filters('modularity/image/mainnews',
                    array(500, 250),
                    $args
                )
            );
        ?>
        <div class="grid-lg-12">
            <a href="<?php echo get_blog_permalink($item->blog_id, $item->ID); ?>" class="<?php echo implode(' ', apply_filters('Modularity/Module/Classes', array('box', 'box-news', 'box-news-horizontal'), $module->post_type, $args)); ?> <?php echo $item->is_sticky ? 'is-sticky' : ''; ?>">
                <?php if ($hasImages) : ?>
                    <div class="box-image-container">
                        <?php if ($thumbnail_image) : ?>
                        <img src="<?php echo $thumbnail_image[0]; ?>" alt="<?php echo $item->post_title; ?>">
                        <?php else : ?>
                        <figure class="image-placeholder"></figure>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div class="box-content">
                    <h3 class="text-highlight"><?php echo apply_filters('the_title', $item->post_title); ?></h3>
                    <time datetime="<?php echo date('Y-m-d H:i:s', strtotime($item->post_date)); ?>"><?php echo date(get_option('date_format'), strtotime($item->post_date)); ?></time>
                    <p><?php echo isset(get_extended($item->post_content)['main']) ? wp_strip_all_tags(get_extended($item->post_content)['main']) : ''; ?></p>
                    <p><span class="link-item"><?php _e('Read more', 'modularity'); ?></span></p>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

