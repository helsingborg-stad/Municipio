<?php
global $post;

$thumbnail = municipio_get_thumbnail_source(
    $post->ID,
    array(500, 500)
);

$gridSizeInt = (int)str_replace('-', '', filter_var($grid_size, FILTER_SANITIZE_NUMBER_INT));
$gridAlterSize = $gridSizeInt * 2;

$columnSize = 'grid-md-' . $gridSizeInt;
$columnHeight = false;

if ($grid_alter) {
    switch ($gridSizeInt) {
        case 3:
            $columnHeight = '280px';
            break;

        case 4:
            $columnHeight = '400px';
            break;

        case 6:
            $columnHeight = '500px';
            break;

        default:
            $columnHeight = false;
            break;
    }

    if ($gridSizeInt !== 12 && floor($postNum % (12 / $gridSizeInt) === 0)) {
        $columnSize = 'grid-md-' . $gridAlterSize;
    }
}
?>
<div class="{{ $columnSize }}">
    <a href="{{ the_permalink() }}" class="box box-post-brick" <?php echo ($columnHeight) ? 'style="padding-bottom:0;height:' . $columnHeight . '"' : ''; ?>>
        @if ($thumbnail)
        <div class="box-image" {!! $thumbnail ? 'style="background-image:url(' . $thumbnail . ');"' : '' !!}>
            <img src="{{ municipio_get_thumbnail_source(null,array(500,500)) }}" alt="{{ the_title() }}">
        </div>
        @endif

        <div class="box-content">
            @if (in_array('category', (array)get_field('archive_' . sanitize_title(get_post_type()) . '_post_display_info', 'option')) && isset(get_the_category()[0]->name))
            <span class="box-post-brick-category">{{ get_the_category()[0]->name }}</span>
            @endif

            @if (get_field('archive_' . sanitize_title(get_post_type()) . '_feed_date_published', 'option') != 'false')
            <span class="box-post-brick-date">
                <time>
                    {{ in_array(get_field('archive_' . sanitize_title(get_post_type()) . '_feed_date_published', 'option'), array('datetime', 'date')) ? the_time(get_option('date_format')) : '' }}
                    {{ in_array(get_field('archive_' . sanitize_title(get_post_type()) . '_feed_date_published', 'option'), array('datetime', 'time')) ? the_time(get_option('time_format')) : '' }}
                </time>
            </span>
            @endif

            <h3 class="post-title">{{ the_title() }}</h3>
        </div>
        <div class="box-post-brick-lead">
            {{ the_excerpt() }}
        </div>
    </a>
</div>
