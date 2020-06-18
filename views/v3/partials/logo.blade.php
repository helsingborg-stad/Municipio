@link([
    'href' => $homeUrl
])
    @if ($logo)
        @logotype([
            'src'=> $logo['url'],
        ])
        @endlogotype
    @else
        @typography([
            'variant' => 'h1',
            'element' => 'span',
            'classList' => ['no-margin', 'no-padding']
        ])
            {{$logo['text']}}
        @endtypography
    @endif
@endlink
{{-- TODO: REMOVE FUNCTIONS. LOCATION: BASE CONTROLLER. --}}
@if (get_field('header_tagline_type', 'option') === 'custom' &&
    get_field('header_tagline_enable', 'option')) 

    @typography([
        'element' => 'span',
        'classList' => ['tagline']
    ])
    {{get_field('header_tagline_text', 'option')}}
    @endtypography

@elseif(get_field('header_tagline_enable', 'option'))

    @typography([
        'element' => 'span',
        'classList' => ['tagline']
    ])
        {{get_bloginfo()}}
    @endtypography

@endif
