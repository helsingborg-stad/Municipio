@if($post->getSchemaProperty('url'))
    @card([
        'heading' => $lang->linksTitle
    ])
        @slot('aboveContent')
            @button([
                'href' => $post->getSchemaProperty('url'),
                'color' => 'primary',
                'style' => 'filled',
                'size' => 'md',
                'icon' => 'open_in_new',
                'fullWidth' => false,
                'text' => $lang->readMore,
                'classList' => [
                    'u-margin__top--2'
                ],
                'attributeList' => [
                    'style' => 'justify-self: start;',
                ],
                'target' => '_blank'
            ])
            @endbutton
        @endslot
    @endcard
@endif