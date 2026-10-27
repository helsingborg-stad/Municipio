@element(['classList' => ['u-position--relative', 'u-aspect-ratio--16-9', 'u-margin__top--4']])
    @image([
        'src' => $imageSrc,
        'alt' => $imageAlt,
        'cover' => true,
        'calculateAspectRatio' => false,
    ])
    @endimage
@endelement
