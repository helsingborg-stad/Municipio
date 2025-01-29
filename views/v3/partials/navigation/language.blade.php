
@card([
    'classList' => [
        'site-language-menu__card',
        'u-padding--2',
        'u-color__bg--default',
    ]
])
    @nav([
        'id' => 'menu-language',
        'items' => $languageMenu['items'],
        'direction' => 'vertical',
        'includeToggle' => false,
        'classList' => ['s-nav-language'],
        'height' => 'sm',
        'expandLabel' => $lang->expand
    ])
    @endnav

    @if($languageMenuOptions->moreLanguageLink)

        @link([
            'href' => $languageMenuOptions->moreLanguageLink,
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
                {{ __('More Languages', 'municipio') }}
            @endtypography

            @icon([
                'icon' => 'arrow_forward',
                'size' => 'md'
            ])
            @endicon
        @endlink

    @endif

    @if($languageMenuOptions->disclaimer)
        @typography([
            'variant' => 'byline',
            'classList' => [
                'u-color__text--dark',
                'u-padding--1'
            ]
        ])
            {{ $languageMenuOptions->disclaimer }}
        @endtypography
    @endif
    
@endcard