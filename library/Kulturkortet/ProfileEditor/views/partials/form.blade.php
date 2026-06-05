@form([
    'method' => 'POST',
    'action' => $saveUrl,
    'classList' => [
        'kulturkortet-profile-editor__form',
        'o-layout-grid',
        'o-layout-grid--gap-6',
        'u-margin__top--5'
    ],
])
    <input type="hidden" name="action" value="save" />
    @field([
        'type' => 'email',
        'name' => 'email',
        'value' => $profile['email'],
        'label' => $lang['emailLabel'],
        'placeholder' => $lang['emailPlaceholder'],
        'fieldAttributeList' => ['autofocus' => 'autofocus'],
        'required' => true
    ])
    @endfield

    @element([
    'classList' => [
        'u-display--flex',
        'u-justify-content--end'
        ] 
    ])
        @button([
            'type' => 'submit',
            'color' => 'primary',
            'text' => $lang['saveUrl'],
        ])
        @endbutton
    @endelement
@endform