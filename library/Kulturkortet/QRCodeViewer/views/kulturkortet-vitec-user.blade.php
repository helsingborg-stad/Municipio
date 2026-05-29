@include('partials.ticket')

<div class="o-grid">
    <div class="o-grid-12@md">
        @card([
                    'color' => 'success',
                    'image' => [
                        'src' => 'https://media.helsingborg.se/uploads/networks/5/sites/194/2020/03/rabatter_kulturkortet_3-1024x576.webp',
                        'alt' => 'Kulturkortet',
                        'square' => true,
                    ],
                    'heading' => $profile['firstname'] . ' ' . $profile['lastname'],
                    'buttons' => [
                            ['type' => 'filled', 'color' => 'primary', 'text' => 'Logga ut', 'href' => $logoutUrl],
                            ...(empty($attributes['profileLinkLabel']) || empty($attributes['profileLink']) ? [] : [['type' => 'filled', 'color' => 'primary', 'text' => $attributes['profileLinkLabel'] ?? '', 'href' => $attributes['profileLink'] ?? '']])
                        ]
                ])
                @slot('subHeading')
                    {{ $ticket['validFrom'] ?? '' }} - {{ $ticket['validTo'] ?? '' }}
                @endslot
                @slot('aboveContent')
                    <canvas data-kulturkortet-barcode="{{ $ticket['barcode'] ?? '' }}"></canvas>
                @endslot
        @endcard
        @if ($showDebugInfo)
            @card([
                'color' => 'info',
                'heading' => 'Debug info - Vitec user data',
            ])
                @slot('content')
                    <pre><code>{{ var_export($vitecUser, true) }}</code></pre>
                @endslot
            @endcard
        @endif
    </div>
</div>
