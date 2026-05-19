<div class="o-grid">
    <div class="o-grid-4@md">
        @card([
                    'color' => 'success',
                    'image' => [
                        'src' => 'https://media.helsingborg.se/uploads/networks/5/sites/194/2020/03/rabatter_kulturkortet_3-1024x576.webp',
                        'alt' => 'Kulturkortet',
                        'square' => true,
                    ],
                    'heading' => $model['name'] ?? '',
                ])
                @slot('subHeading')
                    {{ $model['validFrom'] ?? '' }} - {{ $model['validTo'] ?? '' }}
                @endslot
                @slot('aboveContent')
                    <canvas data-kulturkortet-barcode="{{ $model['barcode'] ?? '' }}"></canvas>
                @endslot
        @endcard
    </div>
</div>
