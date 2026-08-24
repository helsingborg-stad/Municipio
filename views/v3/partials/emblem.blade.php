@if($isFrontPage)
    @logotype([
        'src'=> $emblem,
        'alt' => $lang->emblem,
        'classList' => ['c-logotype--emblem', 'u-display--none@xs', 'u-display--none@sm']
    ])
    @endlogotype
@endif