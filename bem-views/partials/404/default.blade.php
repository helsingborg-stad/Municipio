<div class="creamy">
    <div class="container">
        <div class="grid-lg-12 text-center">
            <div class="gutter gutter-xl">
                <h1 class="error404-title">404</h1>
                <span class="error404-subtitle">{{ get_field('404_error_message', 'option') ? get_field('404_error_message', 'option') : 'The page could not be found' }}</span>
            </div>
        </div>
    </div>
</div>

<div class="container main-container">
    <div class="grid">
        <div class="grid-lg-8" style="margin: 0 auto;">
            <article class="clearfix">

                {!! get_field('404_error_info', 'option') ? get_field('404_error_info', 'option') : '' !!}
            </article>

            <ul class="actions">
                @if (is_array(get_field('404_display', 'option')) && in_array('search', get_field('404_display', 'option')))
                <li>
                    <a rel="nofollow" href="{{ home_url() }}?s={{ $keyword }}" class="link-item">{{ sprintf(get_field('404_display', 'option') ? get_field('404_search_link_text', 'option') : 'Search "%s"', $keyword) }}</a>
                </li>
                @endif

                @if (is_array(get_field('404_display', 'option')) && in_array('home', get_field('404_display', 'option')))
                <li><a href="{{ home_url() }}" class="link-item">{{ get_field('404_home_link_text', 'option') ? get_field('404_home_link_text', 'option') : 'Go to home' }}</a></li>
                @endif

                @if (is_array(get_field('404_display', 'option')) && in_array('back', get_field('404_display', 'option')))
                <li><a href="javascript:history.go(-1);" class="link-item">{{ get_field('404_back_button_text', 'option') ? get_field('404_back_button_text', 'option') : 'Go back' }}</a></li>
                @endif
            </ul>

            @if(is_array($related) && !empty($related))

                <div class="grid">
                    <div class="grid-xs-12">
                         <h3><?php _e("We made a search for you, maybe you were looking for...", 'municipio'); ?></h3>
                    </div>
                </div>

                <div class="grid">
                    <div class="grid-xs-12">
                        <ul class="search-result-list">
                            @foreach($related as $item)
                                <li>
                                    <div class="search-result-item">
                                        <span class="search-result-date">{{ date(get_option('date_format'), strtotime($item->post_date)) }}</span>
                                        <h3><a href="{{ get_permalink($item->ID) }}" class="link-item">{{ $item->post_title }}</a></h3>
                                        <p>{{ wp_trim_words(wp_strip_all_tags($item->post_content, true), 55, "") }}</p>
                                        <div class="search-result-info">
                                            <span class="search-result-url"><i class="fa fa-globe"></i> <a href="{{ get_permalink($item->ID) }}">{{ get_permalink($item->ID) }}</a></span>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
