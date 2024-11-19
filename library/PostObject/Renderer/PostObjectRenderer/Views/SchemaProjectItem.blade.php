@if(!empty($gridColumnClass))
    <div class="{{ $gridColumnClass }}">
@endif

@card([
    'image' => $postObject->imageContract ?? $postObject->images['thumbnail16:9'],
    'link' => $postObject->permalink,
    'heading' => $postObject->postTitle,
    'metaFirst' => true,
    'meta' =>  !empty($postObject->projectTerms['technology']) ? implode(' / ', $postObject->projectTerms['technology']) : '',
    'context' => ['archive', 'archive.list', 'archive.list.card'],
    'containerAware' => true,
    'content' => !empty($postObject->projectTerms['category']) ? implode(' / ', $postObject->projectTerms['category']) : '',
    'hasPlaceholder' => $showPlaceholder && empty($postObject->images['thumbnail16:9']['src']),
    'classList' => ['u-height--100']
])  
    @slot('afterContent')
        @if(isset($postObject->progress))
            @group([
                'direction' => 'vertical',
                'justifyContent' => 'flex-end',
                'classList' => ['u-height--100']
            ])
                @if(isset($postObject->projectTerms['status'][0]))
                    @typography([
                        'element' => 'b',
                        'classList' => ['u-margin__left--auto']
                    ])
                        {{$postObject->projectTerms['status'][0]}}
                    @endtypography
                @endif
                @progressBar([
                    'value' => $postObject->progress,
                ])
                @endprogressBar
            @endgroup
        @endif
    @endslot
@endcard

@if(!empty($gridColumnClass))
    </div>
@endif