@if(!empty($singleMediaItem['embed']))
    @element(['classList' => ['u-position--relative', 'u-margin__bottom--4']])
        {!! $singleMediaItem['embed'] !!}
    @endelement
@else
    @image([
        'src' => $singleMediaItem['image'],
        'caption' => $singleMediaItem['text'] ?? '',
        'calculateAspectRatio' => false,
        'classList' => [
            'u-width--100',
            'u-aspect-ratio--16-9',
            'u-margin__bottom--4'
        ]
    ])
    @endimage
@endif
