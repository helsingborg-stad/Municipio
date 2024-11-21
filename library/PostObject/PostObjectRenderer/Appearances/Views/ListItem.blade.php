@collection__item([ 'displayIcon' => true, 'icon' => 'arrow_forward', 'link' => $postObject->getPermalink() ])
    @typography([ 'element' => 'h2', 'variant' => 'h4' ])
        {{ $postObject->getTitle() }}
    @endtypography
@endcollection__item