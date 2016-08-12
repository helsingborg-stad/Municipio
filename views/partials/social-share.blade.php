<ul class="share share-social share-social-icon-lg share-horizontal share-no-labels hidden-print">
    <li>
        <a class="share-social-facebook" data-action="share-popup" href="https://www.facebook.com/sharer/sharer.php?u={{ get_the_permalink() }}" data-tooltip="<?php _e('Share on', 'municipio'); ?> Facebook">
            <i class="pricon pricon-facebook-square"></i>
            <span><?php _e('Share on', 'municipio'); ?> Facebook</span>
        </a>
    </li>
    <li>
        <a class="share-social-twitter" data-action="share-popup" href="http://twitter.com/share?url=<?php echo urlencode(wp_get_shortlink()); ?>" data-tooltip="<?php _e('Share on', 'municipio'); ?> Twitter">
            <i class="pricon pricon-twitter-square"></i>
            <span><?php _e('Share on', 'municipio'); ?> Twitter</span>
        </a>
    </li>
    <li>
        <a class="share-social-linkedin" data-action="share-popup" href="https://www.linkedin.com/shareArticle?mini=true&amp;url={{ get_the_permalink() }}&amp;title={{ get_the_title() }}&amp;summary={{ get_the_excerpt() }}&amp;source={{ bloginfo('site_name') }}" data-tooltip="<?php _e('Share on', 'municipio'); ?> LinkedIn">
            <i class="pricon pricon-linkedin-square"></i>
            <span><?php _e('Share on', 'municipio'); ?> LinkedIn</span>
        </a>
    </li>
</ul>
