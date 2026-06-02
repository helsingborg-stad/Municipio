@element([
    'classList' => [
        'kulturkortet-ticket',
        'u-rounded--2',
        'u-color__bg--secondary',
        'u-padding--3',
    ],
])
    @element([
        'classList' => [
            'kulturkortet-ticket__inner-card',
            'u-rounded--2',
            'u-padding--3',
        ],
    ])
        @typography([
            'element' => 'h2',
            'variant' => 'h6',
            ])
                {{ $profile['firstname'] . ' ' . $profile['lastname'] }}
        @endtypography
        @if (!empty($ticket['daysLeft']))
            @element([
                'classList' => ['kulturkortet-ticket__remaining-days'],
            ])
                @element([
                    'componentElement' => 'span',
                    'classList' => ['kulturkortet-ticket__remaining-days-number'],
                ])
                    {{ $ticket['daysLeft'] }}
                @endelement
                @element([
                    'componentElement' => 'span',
                    'classList' => ['kulturkortet-ticket__remaining-days-label'],
                ])
                    {{ $lang['days'] }}
                @endelement
            @endelement
        @endif
        @element([
            'classList' => ['kulturkortet-ticket__date-range'],
        ])
            {{ $ticket['validFrom'] ?? '' }} - {{ $ticket['validTo'] ?? '' }}
        @endelement
        @element([
            'componentElement' => 'canvas',
            'classList' => ['kulturkortet-ticket__qr-code'],
            'attributeList' => [
                'data-kulturkortet-barcode' => $ticket['barcode'] ?? '',
            ],
        ])
            <!-- QR code will be rendered here by JavaScript -->
        @endelement
    @endelement
@endelement