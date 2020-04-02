@if (is_active_sidebar('bottom-sidebar'))
    <?php dynamic_sidebar('bottom-sidebar'); ?>
@endif

{{-- <footer id="site-footer" class="c-footer {{ apply_filters('Views/Partials/Header/FooterClass',
$footerLayout['classes']) }}">
  
    Footer area....
    @section('footer-body')

        @if (get_field('footer_logotype_vertical_position', 'option') == 'bottom')

            <nav>
                @includeIf('partials.navigation.helper')
            </nav>

        @endif



                @if (get_field('footer_logotype_vertical_position', 'option') == 'top' || !get_field('footer_logotype_vertical_position', 'option'))

                    @if (get_field('footer_logotype', 'option') != 'hide')
                        {!! municipio_get_logotype(get_field('footer_logotype', 'option'), false, true, false, false) !!}
                    @endif

                    <nav class="{{ !get_field('footer_signature_show', 'option') ? 'pull-right' : '' }}">
                        @includeIf('partials.navigation.helper')
                    </nav>

                @endif




                @if (is_active_sidebar('footer-area'))
             
                @endif


                @if (get_field('footer_logotype_vertical_position', 'option') == 'bottom' && get_field('footer_logotype', 'option') != 'hide')
                    @if (get_field('header_logotype', 'option') === 'negative')
                        @includeIf('partials.logo', [
                            'logo' => get_field('logotype_negative', 'option'),
                            'logoTooltip' => get_field('logotype_tooltip', 'option')
                            ])
                    @else
                        @includeIf('partials.logo', [
                            'logo' => get_field('logotype', 'option'),
                            'logoTooltip' => get_field('logotype_tooltip', 'option')
                            ])
                    @endif
                @endif


            @if (get_field('footer_signature_show', 'option'))
                {!! apply_filters('Municipio/footer_signature', '<a href="http://www.helsingborg.se">' . $footerLogo . '</a>') !!}
            @endif



        @if (have_rows('footer_icons_repeater', 'option'))

            <ul class="icons-list">
                @foreach(get_field('footer_icons_repeater', 'option') as $link)
                    <li>

                        @link([
                            'href' =>  $link['link_url']
                        ])
                            {{$link['link_icon']}}


                            @if (isset($link['link_title']))
                                @typography([
                                    'element' => 'span'
                                ])
                                {{ $link['link_title'] }}
                                @endtypography

                            @endif
                        @endlink

                    </li>
                @endforeach
            </ul>

        @endif

    @stop

</footer> --}}

@footer([
    'logotype' => $logotype->negative['url']
])
    
@endfooter