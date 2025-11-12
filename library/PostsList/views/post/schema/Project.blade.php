@card([
    'image' => $post->getImage(),
    'link' => $post->getPermalink(),
    'heading' => $post->getTitle(),
    'metaFirst' => true,
    'meta' =>  $getSchemaProjectTechnologyTerms($post),
    'context' => ['archive', 'archive.list', 'archive.list.card'],
    'containerAware' => true,
    'content' => $getSchemaProjectCategoryTerms($post),
    'hasPlaceholder' => $appearanceConfig->shouldDisplayPlaceholderImage() && !$post->getImage(),
    'classList' => ['u-height--100']
])
    @slot('afterContent')
        @group([
            'direction' => 'vertical',
            'justifyContent' => 'flex-end',
            'classList' => ['u-height--100']
        ])
            @typography([ 'element' => 'b', 'classList' => ['u-margin__left--auto'] ])
                {{-- {{$getProgressLabel($post)}} --}}
                {{$getSchemaProjectProgressLabel($post)}}
            @endtypography
            @progressBar([ 'value' => $getSchemaProjectProgressPercentage($post) ])@endprogressBar
        @endgroup

    @endslot
@endcard