
@card([
    'classList' => [
        'site-language-menu__card',
        'u-padding--2',
        'u-color__bg--default',
    ]
])
    @nav([
        'items' => $floatingMenuItems,
        'direction' => 'vertical',
        'includeToggle' => false,
        'classList' => ['c-nav--tiles']
    ])
    @endnav

    @link([
        'href' => '#',
        'classList' => [
            'site-language-menu__more',
            'u-display--flex',
            'u-justify-content--space-between',
            'u-padding__y--2',
            'u-margin__top--2'
        ]
    ])
        @typography([
            'variant' => 'h4',
            'classList' => [
                'u-color__text--darkest',
                'u-margin__top--0'
            ]
        ])
            More Languages
        @endtypography

        @icon([
            'icon' => 'arrow_forward',
            'size' => 'md'
        ])
        @endicon
    @endlink

    @typography([
        'variant' => 'byline',
        'classList' => [
            'u-color__text--dark',
            'u-padding--1'
        ]
    ])
        Please bear in mind, since Google Translate is an automatically generated translation, we do not take any responisibility for errors in the text.
    @endtypography
@endcard