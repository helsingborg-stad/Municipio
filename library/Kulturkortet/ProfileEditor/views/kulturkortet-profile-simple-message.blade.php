@element([])
    @typography([
        'element' => 'h2',
        'variant' => 'h4'
    ])
        {{ $lang['heading'] }}
    @endtypography

    @typography([
        'element' => 'p',
    ])
        {{ $lang['content'] }}
    @endtypography

    @button([
        'color' => 'primary',
        'href' => $url,
        'text' => $lang['actionLabel'],
        'classList' => ['u-margin__top--2'],
        'reversePositions' => true,
        'icon' => 'login'
    ])
    @endbutton
@endelement