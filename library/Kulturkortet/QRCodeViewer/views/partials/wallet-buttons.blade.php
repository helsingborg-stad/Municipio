@if (!empty($wallet['apple']) || !empty($wallet['google']))
    @element([
        'classList' => [
            'kulturkortet-wallet-buttons',
            'u-display--flex',
            'o-layout-grid--gap-4',
        ],
    ])
        @if (!empty($wallet['apple']))
        @button([
            'color' => 'primary',
            'href' => $wallet['apple'],
            'text' => $lang['appleWallet'],
            'classList' => ['kulturkortet-wallet-buttons__button'],
            'attributeList' => [
                'data-no-instant' => 'true',
                'data-kulturkortet-apple-wallet' => 'true',
            ],
        ])
        @endbutton
        @endif
        @if (!empty($wallet['google']))
        @button([
            'color' => 'primary',
            'href' => $wallet['google'],
            'text' => $lang['googleWallet'],
            'classList' => ['kulturkortet-wallet-buttons__button'],
        ])
        @endbutton
        @endif
    @endelement
@endif
