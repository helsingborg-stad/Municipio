@button([
    'text' => false,
    'color' => 'default',
    'style' => 'basic',
    'href' => $url,
    'icon' => $media,
    'reversePositions' => 'true',
    'attributeList' => [
        'itemprop' => $media ?? 'socialmedia',
        'title' => $label ? ucfirst($label) : 'Social media',
    ],
    'classList' => ['c-button--some', 'c-button--' . $media, 'u-margin--0', 'u-color__text--black']
])
@endbutton