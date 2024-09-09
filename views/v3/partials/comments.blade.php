@if( is_singular() && comments_open() && get_option('comment_registration') === 0 || is_singular() && comments_open())

    <div class="comment">
        @card([
            'classList' => ['u-padding--3'],
        ])
            {{Municipio\Comment\Form::get()}}
        @endcard 
        <div class="comment__conversation">
            @foreach($comments as $comment)
                @if($comment->comment_parent == 0)
                    
                    @comment([
                        'id' => 'div-comment-' . $comment->comment_ID,
                        'author' => $comment->comment_author,
                        'author_image' => get_user_by('email', $comment->comment_author_email) ? get_avatar_url(get_user_by('email', $comment->comment_author_email)->data->ID ) : false,
                        'text' => get_comment_text($comment->comment_ID),
                        'icon' => 'face',
                        'bubble_color' => 'light',
                        'date_suffix' => $lang->ago,
                        'date' => date("Y-m-d H:i:s", strtotime($comment->comment_date)),
                        'classList' => [
                            'comment-'.$comment->comment_ID, 
                            'c-comment--level-1',
                            'comment-reply-link',
                        ],
                        'dateLabels' => [
                            'year' => $lang->year,
                            'month' => $lang->month,
                            'week' => $lang->week,
                            'day' => $lang->day,
                            'hour' => $lang->hour,
                            'minute' => $lang->minute,
                            'second' => $lang->second
                        ],
                        'dateLabelsPlural' => [
                            'year' => $lang->years,
                            'month' => $lang->months,
                            'week' => $lang->weeks,
                            'day' => $lang->days,
                            'hour' => $lang->hours,
                            'minute' => $lang->minutes,
                            'second' => $lang->seconds
                        ]
                    ])
                        @slot('actions')
                            
                            @if (is_user_logged_in())
                                <div class="comment--likes u-margin__right--2">
                                    @button([
                                        'icon' => 'thumb_up',
                                        'size' => 'sm',
                                        'color' => '',
                                        'style' => 'basic',
                                        'type' => 'button',
                                        'classList' => [
                                            'comment--likes-icon', 
                                            \Municipio\Comment\Likes::likeButton($comment->comment_ID)['classList']
                                        ],
                                        'attributeList' => [
                                            'data-commentid' => $comment->comment_ID,
                                        ],
                                    ])
                                    @endbutton

                                    <span class="c-typography c-typography__variant--meta" data-likes="{{\Municipio\Comment\Likes::likeButton($comment->comment_ID)['count']}}"
                                        id="comment-likes-{{$comment->comment_ID}}">
                                            {{\Municipio\Comment\Likes::likeButton($comment->comment_ID)['count']}}
                                    </span>
                                </div>
                            @endif

                            @button([
                                'size' => 'sm',
                                'icon' => 'reply',
                                'reversePositions' => false,
                                'style' => 'basic',
                                'color' => 'default',
                                'text' => __('Reply', 'municipio'),
                                'componentElement' => 'div',
                                'attributeList' => [
                                    'data-commentid' => $comment->comment_ID,
                                    'data-postid' => $post->id,
                                    'data-belowelement' => 'div-comment-'.$comment->comment_ID,
                                    'data-respondelement' => 'respond',
                                    'rev' => 'nofollow',
                                    'js-toggle-trigger' => 'hide-reply-'.$comment->comment_ID,
                                    'js-toggle-item' => 'hide-reply-'.$comment->comment_ID,
                                    'js-toggle-class' => 'u-display--none'
                                ],
                                'classList' => ['comment-reply-link']
                            ])
                            @endbutton

                        @endslot
                    @endcomment

                    @if (!empty(get_comments(array('parent' => $comment->comment_ID, 'order' => 'asc'))))
                        @foreach(get_comments(array('parent' => $comment->comment_ID, 'order' => 'asc'))  as $answer)
                            @comment([
                                'id' => "comment-" . $answer->comment_ID,
                                'author' => $answer->comment_author,
                                'author_url' => 'mailto:'.$answer->comment_author_email,
                                'author_image' => get_avatar_url(get_user_by('email', $answer->comment_author_email)->data->ID ),
                                'text' => get_comment_text($answer->comment_ID),
                                'icon' => 'face',
                                'bubble_color' => 'light',
                                'date_suffix' => $lang->ago,
                                'date' => date("Y-m-d H:i:s", strtotime($answer->comment_date)),
                                'is_reply' => true,
                                'classList' => [
                                    'c-comment--level-2',
                                ],
                                'dateLabels' => [
                                    'year' => $lang->year,
                                    'month' => $lang->month,
                                    'week' => $lang->week,
                                    'day' => $lang->day,
                                    'hour' => $lang->hour,
                                    'minute' => $lang->minute,
                                    'second' => $lang->second
                                ],
                                'dateLabelsPlural' => [
                                    'year' => $lang->years,
                                    'month' => $lang->months,
                                    'week' => $lang->weeks,
                                    'day' => $lang->days,
                                    'hour' => $lang->hours,
                                    'minute' => $lang->minutes,
                                    'second' => $lang->seconds
                                ]
                            ])
                                @slot('actions')
                                    @if (is_user_logged_in())
                                        <div class="comment--likes">
                                            @button([
                                                'icon' => 'thumb_up',
                                                'size' => 'sm',
                                                'color' => '',
                                                'style' => 'basic',
                                                'type' => 'button',
                                                'classList' => ['comment--likes-icon', \Municipio\Comment\Likes::likeButton($answer->comment_ID)['classList']],
                                                'attributeList' => [
                                                    'data-commentid' => $answer->comment_ID,
                                                ],
                                            ])
                                            @endbutton

                                            <span class="c-typography c-typography__variant--meta" data-likes="{{\Municipio\Comment\Likes::likeButton($answer->comment_ID)['count']}}"
                                                id="comment-likes-{{$answer->comment_ID}}">
                                                {{\Municipio\Comment\Likes::likeButton($answer->comment_ID)['count']}}
                                            </span>
                                        </div>
                                    @endif
                                @endslot
                            @endcomment
                        @endforeach
                    @endif
                @endif
            @endforeach
        </div>
    </div>
@endif