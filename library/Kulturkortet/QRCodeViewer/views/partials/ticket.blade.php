@element([
    'id' => 'kulturkortet-ticket',
])
    @typography([
        'element' => 'h2',
        'variant' => 'h6',
        'content' => 'Ditt Kulturkort',
        'classList' => ['u-mb-1'],
        ])
            {{ $profile['firstname'] . ' ' . $profile['lastname'] }}
    @endtypography
@endelement