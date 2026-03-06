@button([
    'classList' => ['u-margin__top--4'],
    'fullWidth' => true,
    'text' => $lang->apply,
    'color' => 'primary',
    'style' => 'filled',
    'attributeList' => $expired ? ['disabled' => ''] : null,
    'href' => $expired ? null : $post->getSchemaProperty('url')
])@endbutton
