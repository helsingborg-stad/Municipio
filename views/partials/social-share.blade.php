<div class="social-share">
    <ul>
        <li>
            <a data-action="share-popup" href="https://www.facebook.com/sharer/sharer.php?u={{ get_the_permalink() }}" title="{{ __('Dela på Facebook', 'municipio') }}">
                <i class="fa fa-facebook"></i>
                <span>{{ __('Dela på Facebook', 'municipio') }}</span>
            </a>
        </li>
        <li>
            <a data-action="share-popup" href="http://twitter.com/share?url=<?php echo urlencode(wp_get_shortlink()); ?>" title="{{ __('Dela på Twitter', 'municipio') }}">
                <i class="fa fa-twitter"></i>
                <span>{{ __('Dela på Twitter', 'municipio') }}</span>
            </a>
        </li>
        <li>
            <a data-action="share-popup" href="https://www.linkedin.com/shareArticle?mini=true&amp;url={{ get_the_permalink() }}&amp;title={{ get_the_title() }}&amp;summary={{ get_the_excerpt() }}&amp;source={{ bloginfo('site_name') }}" title="{{ __('Dela på LinkedIn', 'municipio') }}">
                    <i class="fa fa-linkedin"></i>
                    <span>{{ __('Dela på LinkedIn', 'municipio') }}</span>
                </a>
        </li>
    </ul>
</div>
