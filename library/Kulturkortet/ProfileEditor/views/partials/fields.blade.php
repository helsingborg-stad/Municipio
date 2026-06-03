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
