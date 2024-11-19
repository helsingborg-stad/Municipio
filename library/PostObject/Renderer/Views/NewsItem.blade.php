@if(!empty($gridColumnClass))
    <div class="{{ $gridColumnClass }}">
@endif

@link([
    'href' => $postObject->getPermalink(),
    'slot' => ''
])
    @segment([
        'layout' => 'col-left',
        'title' => $postObject->getTitle(),
        'height' => 'sm',
        'overlay' => 'blur'
    ])
        @slot('top')
            <span class="c-segment__top-date"> {{date_i18n('l d F Y', strtotime($postObject->postDate))}} </span>
        @endslot

        @if($postObject->imageContract) 
            @image([
                'src' => $postObject->imageContract
            ])
            @endimage
        @else 
            @image([
                'src' => $postObject->images['thumbnail16:9']['src'],
                'alt' => $postObject->images['thumbnail16:9']['alt'],
            ])
            @endimage
        @endif
    @endsegment
@endlink

@if(!empty($gridColumnClass))
    </div>
@endif