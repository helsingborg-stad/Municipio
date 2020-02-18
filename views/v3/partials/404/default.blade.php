<div class="creamy">
    <div class="container">
        <div class="grid-lg-12 text-center">
            <div class="gutter gutter-xl">
                <h1 class="error404-title">{{ $heading }}</h1>
                <span class="error404-subtitle">{{ $subheading }}</span>
            </div>
        </div>
    </div>
</div>

<div class="container main-container">
    <div class="grid">
        <div class="grid-lg-8" style="margin: 0 auto;">
            <article class="clearfix">

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

            

        </div>
    </div>
</div>

@if($errorMessage) 
    <pre style="padding: 16px;"><h3>{{ $debugHeading }}</h3>{{ $errorMessage }}</pre>
@endif