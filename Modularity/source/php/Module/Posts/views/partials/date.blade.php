@if($post->postDateFormatted)
    @typography(['variant' => 'meta', 'element' => 'span', 'classList' => [$baseClass . '__date']])
        @group
            @icon(['icon' => 'date_range', 'size' => 'sm'])
            @endicon
            @date([
                'action' => 'formatDate',
                'timestamp' => $post->postDateFormatted
            ])
            @enddate
        @endgroup
    @endtypography
@endif
