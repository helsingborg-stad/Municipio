@typography(['element' => 'h3'])
    {!! $lang->galleryLabel !!}
@endtypography
@gallery([
    ...$galleryComponentAttributes,
    'classList' => [
        'u-margin__bottom--6',
        'u-margin__top--4'
    ]
])
@endgallery