@paper([
    'classList' => [
        'u-padding__x--3',
        'u-padding__y--5',
        'u-padding__x--8@md',
        'u-padding__y--8@md',
        'u-padding__x--8@lg',
        'u-padding__y--8@lg',
        'u-padding__x--8@xl',
        'u-padding__y--8@xl'
    ]])
    @typography([ 'element' => 'h2', 'variant' => 'h2', 'classList' => ['u-margin__top--0'], 'attributeList' => ['style' => 'color: var(--color-secondary-contrasting);'] ])
        {!! $lang->relatedEventsTitle !!}
    @endtypography
    @include('posts-list', $postsListData)
@endpaper