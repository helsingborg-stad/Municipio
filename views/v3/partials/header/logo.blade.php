{{-- TODO: Controller to take care of data --}}
@php
    // Fetching logo data
    $logo = municipio_get_logotype (
                get_field('header_logotype', 'option'),
                get_field('logotype_tooltip', 'option'), true,
                get_field('header_tagline_enable', 'option')
    );
@endphp

@link([
    'href' => $logo['url']
])
    @if ($logo['src'])
        @logotype([
            'src'=> $logo['src'],
            'attributeList' => $logo['attributeList'],
            'classList' => $logo['classList']
        ])
        @endlogotype
    @else
        @typography([
            'variant' => 'h1',
            'element' => 'span',
            'classList' => ['no-margin', 'no-padding']
        ])
            $logo['text']
        @endtypography
    @endif
@endlink

@if ($logo['tagline'])
    <span class="tagline">{{$logo['tagline']}}</span>
@endif