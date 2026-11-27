@element([
    'attributeList' => [
        'data-js-chat-heading-area' => true
    ],
    'classList' => ['u-display--flex', 'o-layout-grid--gap-2'],
])
    @typography([
        'element' => 'h2',
        'variant' => 'h6',
        'attributeList' => [
            'data-js-chat-title' => true,
        ],
        'classList' => ['o-layout-grid--align-center'],
    ])
        <!-- insert name -->
    @endtypography
@endelement
@element([
    'attributeList' => [
        'data-js-chat-actions' => true,
    ],
    'classList' => ['u-margin__left--auto', 'o-layout-grid--align-center', 'u-display--flex']
])
<!-- Chat actions -->
@endelement