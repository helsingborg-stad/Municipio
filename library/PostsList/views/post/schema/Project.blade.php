@card([
    'image' => $post->getImage(),
    'link' => $post->getPermalink(),
    'heading' => $post->getTitle(),
    'metaFirst' => true,
    'meta' =>  $getSchemaProjectTechnologyTerms($post),
    'context' => ['archive', 'archive.list', 'archive.list.card'],
    'containerAware' => true,
    'content' => $getSchemaProjectCategoryTerms($post),
    'hasPlaceholder' => $shouldDisplayPlaceholderImage($post),
    'classList' => ['u-height--100'],
    'attributeList' => ['data-js-posts-list-item' => true],
])
    @slot('afterContent')
        @group([
            'direction' => 'vertical',
            'justifyContent' => 'flex-end',
            'classList' => ['u-height--100']
        ])
            @typography([ 'element' => 'b', 'classList' => ['u-margin__left--auto'] ])
                {{$getSchemaProjectProgressLabel($post)}}
            @endtypography
            @progressBar([ 'value' => $getSchemaProjectProgressPercentage($post) ])@endprogressBar
        @endgroup

    @endslot
@endcard