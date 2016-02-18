<footer class="main-footer">
    <div class="container">

        <!-- Logotype -->
        @if (get_field('footer_logotype_vertical_position', 'option') == 'top' || get_field('footer_logotype_vertical_position', 'option') == '' || get_field('footer_logotype_vertical_position', 'option') == false)
        <div class="grid">
            <div class="grid-lg-12 {{ ($pos = get_field('footer_logotype_horizontal_position', 'option')) ? 'text-' . $pos : '' }}">
                {!! municipio_get_logotype(get_field('footer_logotype', 'option')) !!}
            </div>
        </div>
        @endif

        <!-- Widgets -->
        <div class="grid">
            @if (is_active_sidebar('footer-area'))
                <?php dynamic_sidebar('footer-area'); //Blade not working here? ?>
            @endif
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

        @if (get_field('footer_logotype_vertical_position', 'option') == 'bottom')
        <div class="grid">
            <div class="grid-lg-12 {{ ($pos = get_field('footer_logotype_horizontal_position', 'option')) ? 'text-' . $pos : '' }}">
                {!! municipio_get_logotype(get_field('footer_logotype', 'option')) !!}
            </div>
        </div>
        @endif

    </div>
</footer>
