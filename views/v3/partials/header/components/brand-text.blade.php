@if ($headerBrandEnabled && !empty($brandText))
    @link([
        'href' => $homeUrl, 
        'classList' => ['u-no-decoration', 'c-header__brand-text']
    ])
        @foreach ($brandText as $text)
            @typography([
                    'element' => 'span',
                    'classList' => ['c-header__brand-text-inner']
                ])
                {{ $text }}
            @endtypography
        @endforeach
    @endlink
@endif