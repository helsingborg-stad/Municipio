<?php
$cache = new Municipio\Helper\Cache('intranet-news', array(
    Intranet\User\Subscription::getSubscriptions(get_current_user_id()),
    $display,
    $limit,
    $helpTooltip,
    $news,
    $module,
    $sites
), 5*60);
?>

@if ($cache->start())
@if (count($news) > 0)

    <div class="grid intranet-news-header">
        @if (!$hideTitle && !empty($post_title))
        <div class="grid-xs-12">
            <h2>{!! apply_filters('the_title', $post_title) !!}</h2>
            @if(get_current_blog_id() != BLOG_ID_CURRENT_SITE)
                {!! wp_dropdown_categories($categoryDropdownArgs) !!}
            @endif
        </div>
        @endif
    </div>

    <div class="grid intranet-news" data-infinite-scroll-callback="{{ rest_url('intranet/1.0/news/') }}" data-infinite-scroll-pagesize="{{ $limit }}" data-infinite-scroll-sites="{{ $sites }}" data-module="{{ htmlentities(json_encode($module)) }}" data-args="{!! htmlentities(json_encode($args)) !!}">
        <?php
            echo municipio_intranet_walkthrough(
                __('News feed', 'municipio-intranet'),
                __('This is your personalized news feed. The personalized news feed will show the news that affects all employees. You will also see news from your administration unit (if existing) and from other intranets that you are following.', 'municipio-intranet'),
                '.intranet-news',
                'top-center'
            );
        ?>

        <?php $i = 0; ?>
        @foreach ($news as $item)
            <?php $i++; ?>
            @include('news-item')
        @endforeach
    </div>

    @if ($args['id'] !== 'right-sidebar')
    <div class="grid">
        <div class="grid-lg-12">
            <button class="btn btn-primary btn-block" data-action="intranet-news-load-more"><?php _e('Load more news', 'municipio-intranet'); ?></button>
        </div>
    </div>
@endif
@else
    <div class="grid">
        @if (!empty($module->post_title))
        <div class="grid-xs-12">
            <h2>{{ $module->post_title }}</h2>
        </div>
        @endif

        <div class="grid-xs-12">
            <?php _e('Threre\'s no news stories to display', 'municipio-intranet'); ?> <i class="pricon pricon-smiley-sad"></i>
        </div>
    </div>
@endif
@endif

<?php $cache->stop(); ?>
