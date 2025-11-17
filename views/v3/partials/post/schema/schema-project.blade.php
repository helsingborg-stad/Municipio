{{-- Deprecated in favor of the PostsList feature --}}
@if ($posts)
    <div class="o-grid">
        @foreach ($posts as $key => $post)
             <div class="{{ $gridColumnClass }}">
                @card([
                    'image' => $post->imageContract ?? $post->images['thumbnail16:9'],
                    'link' => $post->permalink,
                    'heading' => $post->postTitle,
                    'metaFirst' => true,
                    'meta' =>  $getTermsList($post, 'project_meta_technology'),
                    'context' => ['archive', 'archive.list', 'archive.list.card'],
                    'containerAware' => true,
                    'content' => $getTermsList($post, 'project_meta_category'),
                    'hasPlaceholder' => $anyPostHasImage && empty($post->images['thumbnail16:9']['src']),
                    'classList' => ['u-height--100']
                ])  
                    @slot('afterContent')

                        @group([
                            'direction' => 'vertical',
                            'justifyContent' => 'flex-end',
                            'classList' => ['u-height--100']
                        ])
                            @typography([ 'element' => 'b', 'classList' => ['u-margin__left--auto'] ])
                                {{$getProgressLabel($post)}}
                            @endtypography
                            @progressBar([
                                'value' => $getProgressPercentage($post),
                            ])
                            @endprogressBar
                        @endgroup

                    @endslot
                @endcard
            </div>
        @endforeach
    </div>
@endif