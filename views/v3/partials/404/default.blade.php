@typography([
    "element" => "h1",
])
    {{ $heading }}
@endtypography

@typography([
    "element" => "span",
])
    {{ $subheading }}
@endtypography
{{-- 
<ul class="actions">


    @if (is_array(get_field('404_display', 'option')) &&
            in_array('home', get_field('404_display', 'option')))
        <li>
            @link([
                'href' => {{ home_url() }}
            ])
                {{ get_field('404_home_link_text', 'option') ?
                        get_field('404_home_link_text', 'option') : 'Go to home' }}
            @endlink
        </li>
    @endif

    @if (is_array(get_field('404_display', 'option')) &&
        in_array('back', get_field('404_display', 'option')))
        <li>
            @link([
                'href' => 'javascript:history.go(-1);'
            ])
                {{ get_field('404_back_button_text', 'option') ?
                        get_field('404_back_button_text', 'option') : 'Go back' }}
            @endlink
        </li>
    @endif
</ul>

--}}

@if($errorMessage)
    @typography([
        "element" => "h3",
    ])
        {{ $debugHeading }}
    @endtypography

    @code(['language' => 'php', 'content' => ''])
        {{ $errorMessage }}
    @endcode
@endif