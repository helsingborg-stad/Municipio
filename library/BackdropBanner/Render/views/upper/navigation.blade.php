@element([
    'classList' => [
        'o-layout-grid',
        'o-layout-grid--cq',
        'backdrop-banner__navigation',
    ],
    'attributeList' => [
        'style' => '  grid-template-columns: repeat(auto-fit, minmax(22rem, 1fr));'
    ]
])
    @foreach($rows as $index => $row)
        @element([
            'classList' => ['backdrop-banner__navigation-item']
        ])
            @typography([
                'element' => 'h2',
                'variant' => 'h1',
                'classList' => ['backdrop-banner__navigation-item-title'],
            ])
                {{ !empty($row['title']) ? $row['title'] : 'Row' . ($index + 1) }}
            @endtypography
            @if(!empty($row['description']))
                @typography([
                    'element' => 'p',
                    'variant' => 'body',
                    'classList' => ['backdrop-banner__navigation-item-description'],
                ])
                    {{ $row['description']}}
                @endtypography
            @endif
        @endelement
    @endforeach
@endelement