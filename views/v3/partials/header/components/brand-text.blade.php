@if ($headerBrandEnabled && !empty($brandText))
    @link([
        'href' => $homeUrl, 
        'classList' => ['u-no-decoration', 'c-header__brand-text']
    ])
        @foreach ($brandText as $text)
            <span>{!! $text !!}</span>
        @endforeach
    @endlink
@endif