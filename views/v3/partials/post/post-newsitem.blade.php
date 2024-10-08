@if ($posts)
    <div class="arcive-news-items o-grid">
        @foreach($posts as $post)
            <div class="{{ $gridColumnClass }}">
                @link([
                    'href' => get_the_permalink(),
                    'slot' => ''
                ])
                    @segment([
                        'layout' => 'col-left',
                        'title' => $post->postTitle,
                        'sub_title' => $post->excerpt,
                        'height' => 'sm',
                        'overlay' => 'blur'
                    ])
                        @slot('top')
                            <span class="c-segment__top-date"> {{date_i18n('l d F Y', strtotime($post->postDate))}} </span>
                        @endslot

                        @if($post->imageContract) 
                            @image([
                                'src' => $post->imageContract
                            ])
                            @endimage
                        @else 
                            @image([
                                'src' => $post->images['thumbnail16:9']['src'],
                                'alt' => $post->images['thumbnail16:9']['alt'],
                            ])
                            @endimage
                        @endif
                    @endsegment
                @endlink
            </div>
        @endforeach
    </div>
@endif


