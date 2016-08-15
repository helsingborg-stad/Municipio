<?php
global $page;
$image = false;

if (has_post_thumbnail($page->ID)) {
    $image = wp_get_attachment_image_src(
        get_post_thumbnail_id($page->ID),
        apply_filters('Modularity/index/image',
            array(80, 80)
        )
    );
    $alt_text = get_post_meta($image_id, '_wp_attachment_image_alt', true);
}
?>

<div class="share creamy material hidden-print clearfix">
    <?php if ($image) : ?>
    <div class="share-image material-radius" style="background-image:url('<?php echo $image[0]; ?>');"></div>
    <?php endif; ?>

    <div class="share-info">
        <strong><?php _e('Share page', 'municipio'); ?>:</strong> {{ the_title() }}
        <ul class="share-social share-social-icon-md share-horizontal share-no-labels">
            <li>
                <a class="share-social-facebook" data-action="share-popup" href="https://www.facebook.com/sharer/sharer.php?u={{ get_the_permalink() }}" data-tooltip="<?php _e('Share on', 'municipio'); ?> Facebook">
                    <i class="pricon pricon-facebook"></i>
                    <span><?php _e('Share on', 'municipio'); ?> Facebook</span>
                </a>
            </li>
            <li>
                <a class="share-social-twitter" data-action="share-popup" href="http://twitter.com/share?url=<?php echo urlencode(wp_get_shortlink()); ?>" data-tooltip="<?php _e('Share on', 'municipio'); ?> Twitter">
                    <i class="pricon pricon-twitter"></i>
                    <span><?php _e('Share on', 'municipio'); ?> Twitter</span>
                </a>
            </li>
            <li>
                <a class="share-social-linkedin" data-action="share-popup" href="https://www.linkedin.com/shareArticle?mini=true&amp;url={{ get_the_permalink() }}&amp;title={{ get_the_title() }}&amp;summary={{ get_the_excerpt() }}&amp;source={{ bloginfo('site_name') }}" data-tooltip="<?php _e('Share on', 'municipio'); ?> LinkedIn">
                    <i class="pricon pricon-linkedin"></i>
                    <span><?php _e('Share on', 'municipio'); ?> LinkedIn</span>
                </a>
            </li>
        </ul>
    </div>
</div>
