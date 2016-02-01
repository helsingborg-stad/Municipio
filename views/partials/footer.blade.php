<footer class="main-footer">
    <div class="container">

        <!-- Logotype -->
        <div class="grid">
            <div class="grid-lg-12">
                {!! municipio_get_logotype(get_field('footer_logotype', 'option')) !!}
            </div>
        </div>

        <!-- Widgets -->
        <div class="grid">
            {!! dynamic_sidebar('footer-area'); !!}
        </div>

        <!-- Footer links -->
        @if(function_exists('have_rows'))
            @if(have_rows('footer_icons_repeater', 'option'))
                <div class="grid">
                    <div class="grid-xs-12">
                        <ul class="icons-list text-center gutter-margin text-xl">
                            @foreach(get_field('footer_icons_repeater', 'option') as $link)
                                <li>
                                    <a href="{{ $link['link_url'] }}" target="_blank" class="link-item-light">
                                        {!! $link['link_icon'] !!}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        @endif

    </div>
</footer>
