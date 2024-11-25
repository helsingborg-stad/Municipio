@element([
    'classList' => array_merge([
        'user', 
        'user--inactive', 
        !empty($customizer->loginLogoutColorScheme) ? 'user--' . $customizer->loginLogoutColorScheme : ''
    ], $classList ?? [])
])
    @button([
        'text' => $lang->login,
        'color' => 'basic',
        'icon' => 'login',
        'style' => 'basic',
        'reversePositions' => true,
        'attributeList' => [
            'data-open' => 'm-search-modal__trigger',
        ],
        'classList' => ['user__link']
    ])
    @endbutton
@endelement