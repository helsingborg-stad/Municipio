<div class="container text-center">
    <div class="grid">
        <div class="grid-lg-2 grid-md-2 grid-sm-12 hidden-xs hidden-sm no-margin no-padding"></div>
        <div class="grid-lg-8 grid-md-8 grid-sm-12">
           <span class="h1 no-margin no-padding">{{ get_site_option('site_name') }}</span>
        </div>
    </div>
</div>
<div class="container main-container text-center">
    <div class="grid">
        <div class="grid-lg-3 grid-md-2 hidden-xs hidden-sm no-margin no-padding"></div>
        <div class="grid-lg-6 grid-md-8 grid-sm-12">
            <h1>404 <em>{{ get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_error_message') ? get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_error_message') : 'The page could not be found' }}</em></h1>

            <ul class="actions">
                @if (is_array(get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_display')) && in_array('search', get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_display')))
                <li>
                    <a rel="nofollow" href="{{ home_url() }}?s={{ $keyword }}" class="link-item link-item-light">{{ sprintf(get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_display') ? get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_search_link_text') : 'Search "%s"', $keyword) }}</a>
                </li>
                @endif

                @if (is_array(get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_display')) && in_array('home', get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_display')))
                <li><a href="{{ home_url() }}" class="link-item link-item-light">{{ get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_home_link_text') ? get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_home_link_text') : 'Go to home' }}</a></li>
                @endif
            </ul>

            {!! get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_error_info') ? get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_error_info') : '' !!}

            @if (is_array(get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_display')) && in_array('back', get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_display')))
            <p>
                <a href="javascript:history.go(-1);" class="btn btn-primary">
                    <i class="fa fa-arrow-circle-o-left"></i>
                    {{ get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_back_button_text') ? get_blog_option(BLOG_ID_CURRENT_SITE, 'options_404_back_button_text') : 'Go back' }}
                </a>
            </p>
            @endif
        </div>
    </div>
</div>
