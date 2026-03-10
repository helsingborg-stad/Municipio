@dump($rows)
@element([
    'classList' => [
        'o-layout-grid',
        'o-layout-grid--cq',
        'o-layout-grid--cols-2',
        'o-layout-grid--cols-4@cq-lg',
    ]
])
    @foreach($rows as $index => $row)
        @element([])
            @typography([
                'element' => 'h2',
                'variant' => 'h3',
            ])
                {{ !empty($row['title']) ? $row['title'] : 'Row' . ($index + 1) }}
            @endtypography
            @if(!empty($row['description']))
                @typography([
                    'element' => 'p',
                    'variant' => 'body',
                ])
                    {{ $row['description']}}
                @endtypography
            @endif
        @endelement
    @endforeach
@endelement