@if ($posts)
    <div class="o-grid">
        @foreach ($posts as $key => $post)
             <div class="{{ $gridColumnClass }}">
                @card([
                    'image' => $post->images['thumbnail16:9'],
                    'link' => $post->permalink,
                    'imageFirst' => true,
                    'heading' => $post->postTitle,
                    'metaFirst' => true,
                    'meta' =>  !empty($post->projectTerms['category']) ? implode('/ ', $post->projectTerms['category']) : '',
                    'context' => ['archive', 'archive.list', 'archive.list.card'],
                    'containerAware' => true,
                    'content' => !empty($post->projectTerms['technology']) ? implode('/ ', $post->projectTerms['technology']) : '',
                    'hasPlaceholder' => $anyPostHasImage && empty($post->images['thumbnail16:9']['src']),
                    'classList' => ['u-height--100', 'c-card--flat', 'project-card'],
                    'attributeList' => ['style' => 'z-index:' . (999 - $key) . ';'],
                ])  
                    @slot('afterContent')
                        @if(isset($post->progressbar))
                            <div class="u-margin__top--auto u-width--100">
                            @if(isset($post->projectTerms['status'][0]))
                                @tooltip([
                                    'label' => $post->projectTerms['status'][0],
                                    'placement' => 'bottom',
                                    'classList' => ['u-justify-content--end']
                                ])
                                @endtooltip
                                @endif
                                @progressBar([
                                    'value' => $post->progressbar,
                                ])
                                @endprogressBar
                            </div>
                        @endif
                    @endslot
                @endcard
            </div>
        @endforeach
    </div>
@endif