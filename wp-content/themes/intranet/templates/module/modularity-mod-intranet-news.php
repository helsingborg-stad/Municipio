<?php
    $news = false;
    $display = get_field('display', $module->ID);
    $limit = !empty(get_field('limit', $module->ID)) ? get_field('limit', $module->ID) : 10;
    $helpTooltip = false;
    $sites = null;

    // Get sites to get news from
    switch ($display) {
        default:
        case 'network_subscribed':
            if (is_user_logged_in()) {
                $sites = array_merge(
                    (array) \Intranet\User\Subscription::getForcedSubscriptions(true),
                    (array) \Intranet\User\Subscription::getSubscriptions(null, true)
                );
            } else {
                $sites = 'all';
            }

            break;

        case 'network':
            $sites = 'all';
            break;

        case 'blog':
            $sites = (array) get_current_blog_id();
            break;
    }

    $cache = new Municipio\Helper\Cache('intranet-news', array(
        Intranet\User\Subscription::getSubscriptions(get_current_user_id()),
        $display,
        $limit,
        $helpTooltip,
        $news
    ), 60*60*3);

    if ($cache->start()) {

        // Fetch the news
        $news = \Intranet\CustomPostType\News::getNews($limit, $sites);

        if (count($news) > 0) :

        $hasImages = false;

        if (get_field('placeholders', $module->ID)) {
            foreach ($news as $item) {
                if (get_thumbnail_source($item->ID) !== false) {
                    $hasImages = true;
                }
            }
        }

        $scrollCallbackSites = $sites;

        if (is_array($scrollCallbackSites)) {
            $scrollCallbackSites = implode(',', $sites);
        }
    ?>
    <div class="grid intranet-news" data-infinite-scroll-callback="<?php echo rest_url('intranet/1.0/news/'); ?>" data-infinite-scroll-pagesize="<?php echo $limit; ?>" data-infinite-scroll-sites="<?php echo $scrollCallbackSites; ?>" data-module="<?php echo htmlentities(json_encode($module)); ?>" data-args="<?php echo htmlentities(json_encode($args)); ?>">

        <?php
        if ($helpTooltip) {
            echo municipio_intranet_walkthrough(
                __('News feed', 'municipio-intranet'),
                __('This is your personalized news feed. The personalized news feed will show the news that affects all employees. You will also see news from your administration unit (if existing) and from other intranets that you are following.', 'municipio-intranet'),
                '.intranet-news',
                'top-center'
            );
        }
        ?>


        <?php if (!$module->hideTitle) : ?>
        <div class="grid-xs-12">
            <h2><?php echo $module->post_title; ?></h2>
        </div>
        <?php endif; ?>

        <?php
        $i = 0;
        foreach ($news as $item) {
            include 'modularity-mod-intranet-news-item.php';
            $i++;
        }
        ?>
    </div>

    <?php if ($args['id'] !== 'right-sidebar' && count($news) >= $limit) : ?>
    <div class="grid">
        <div class="grid-lg-12">
            <button class="btn btn-primary btn-block" data-action="intranet-news-load-more"><?php _e('Load more news', 'municipio-intranet'); ?></button>
        </div>
    </div>
    <?php endif; ?>

    <?php else : ?>

    <div class="grid">
        <?php if (!empty($module->post_title)) : ?>
        <div class="grid-xs-12">
            <h2><?php echo $module->post_title; ?></h2>
        </div>
        <?php endif; ?>
        <div class="grid-xs-12">
            <?php _e('Threre\'s no news stories to display', 'municipio-intranet'); ?> <i class="pricon pricon-smiley-sad"></i>
        </div>
    </div>

    <?php endif; ?>


    <?php

    $cache->stop();
}

?>
