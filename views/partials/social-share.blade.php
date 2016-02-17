<ul class="share share-social share-social-icon-lg share-horizontal share-no-labels">
    <li>
        <a class="share-social-facebook" data-action="share-popup" href="https://www.facebook.com/sharer/sharer.php?u={{ get_the_permalink() }}" data-tooltip="{{ __('Dela på Facebook', 'municipio') }}">
            <i class="fa fa-facebook-square"></i>
            <span>{{ __('Dela på Facebook', 'municipio') }}</span>
        </a>
    </li>
    <li>
        <a class="share-social-twitter" data-action="share-popup" href="http://twitter.com/share?url=<?php echo urlencode(wp_get_shortlink()); ?>" data-tooltip="{{ __('Dela på Twitter', 'municipio') }}">
            <i class="fa fa-twitter-square"></i>
            <span>{{ __('Dela på Twitter', 'municipio') }}</span>
        </a>
    </li>
    <li>
        <a class="share-social-linkedin" data-action="share-popup" href="https://www.linkedin.com/shareArticle?mini=true&amp;url={{ get_the_permalink() }}&amp;title={{ get_the_title() }}&amp;summary={{ get_the_excerpt() }}&amp;source={{ bloginfo('site_name') }}" data-tooltip="{{ __('Dela på LinkedIn', 'municipio') }}">
                <i class="fa fa-linkedin-square"></i>
                <span>{{ __('Dela på LinkedIn', 'municipio') }}</span>
            </a>
    </li>
</ul>
