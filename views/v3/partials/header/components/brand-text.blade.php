@if ($headerBrandEnabled && !empty($brandText))
    @link([
        'href' => $homeUrl, 
        'classList' => array_merge(['u-no-decoration', 'c-header__brand-text'], $menuItem->getCssClasses())
    ])
        @foreach ($brandText as $text)
            <span>{!! $text !!}</span>
        @endforeach
    @endlink
@endif