@scope(['name' => ['language-menu']])
    @card([
        'classList' => [
            'site-language-menu__card'
        ],
    ])
        <div class="c-card__header site-language-menu__header">
            @typography([
                'element' => 'p',
                'variant' => 'h6',
                'classList' => [
                    'u-margin__top--0',
                    'u-margin__bottom--0'
                ]
            ])
                {{ $languageMenuOptions->headline }}
            @endtypography
        </div>

        <div class="c-card__body site-language-menu__body u-padding__top--0">

            @if ($languageMenuOptions->displayCurrentLanguage)
                @button([
                    'variant' => 'filled',
                    'size' => 'md',
                    'text' => $languageMenuOptions->currentLanguage,
                    'classList' => [
                        'site-language-menu__default_lang',
                        'u-margin__bottom--1',
                        'u-width--100'
                    ],
                    'attributeList' => [
                        'disabled' => 'disabled',
                    ]
                ])
                @endbutton
            @endif

            @nav([
                'id' => 'menu-language',
                'items' => $languageMenu['items'],
                'direction' => 'vertical',
                'includeToggle' => false,
                'classList' => ['s-nav-language'],
                'height' => 'md',
                'expandLabel' => $lang->expand
            ])
            @endnav

            @if($languageMenuOptions->moreLanguageLink)
                @button([
                    'icon' => 'arrow_forward',
                    'reversePositions' => true,
                    'text' => $languageMenuOptions->moreLanguageLinkLabel,
                    'color' => 'default',
                    'style' => 'basic',
                    'size' => 'sm',
                    'href' => $languageMenuOptions->moreLanguageLink,
                    'classList' => [
                        'site-language-menu__more'
                    ]
                ])
                @endbutton
            @endif

            @if($languageMenuOptions->disclaimer)
                @typography([
                    'variant' => 'byline',
                    'classList' => [
                        'u-border__top--1',
                        'u-padding__top--2',
                    ]
                ])
                    {{ $languageMenuOptions->disclaimer }}
                @endtypography
            @endif

        </div>
        
    @endcard
@endscope