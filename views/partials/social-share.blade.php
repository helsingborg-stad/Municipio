<!-- Social -->
<ul class="share share-social share-social-icon-md share-horizontal share-no-labels hidden-print inline-block">
    <li>
        <a class="share-social-facebook" data-action="share-popup" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo str_replace('/wp/','/',site_url()."/".urlencode($_SERVER['REQUEST_URI'])); ?>" data-tooltip="<?php _e('Share on', 'municipio'); ?> Facebook">
            <i class="pricon pricon-facebook"></i>
            <span><?php _e('Share on', 'municipio'); ?> Facebook</span>
        </a>
    </li>
    <li>
        <a class="share-social-twitter" data-action="share-popup" href="http://twitter.com/share?url={!! urlencode(get_permalink()) !!}" data-tooltip="<?php _e('Share on', 'municipio'); ?> Twitter">
            <i class="pricon pricon-twitter"></i>
            <span><?php _e('Share on', 'municipio'); ?> Twitter</span>
        </a>
    </li>
    <li>
        <a class="share-social-linkedin" data-action="share-popup" href="https://www.linkedin.com/shareArticle?mini=true&amp;url={!! urlencode(get_permalink()) !!}&amp;title={{ urlencode(get_permalink()) }}" data-tooltip="<?php _e('Share on', 'municipio'); ?> LinkedIn">
            <i class="pricon pricon-linkedin"></i>
            <span><?php _e('Share on', 'municipio'); ?> LinkedIn</span>
        </a>
    </li>
    <li>
        <a class="share-social-email" data-action="share-email" href="#modal-target-{{ get_the_ID() }}" data-tooltip="<?php _e('Share as e-mail', 'municipio'); ?>">
            <i class="pricon pricon-email"></i>
            <span><?php _e('Share with e-mail', 'municipio'); ?></span>
        </a>
        @include('partials.share-email-form')
    </li>
</ul>
