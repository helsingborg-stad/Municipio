@if( is_singular() && comments_open() && get_option('comment_registration') === 0 || is_singular()
 && comments_open() && is_user_logged_in())

    <div class="comment">
        {{Municipio\Comment\CommentsActions::getInitialCommentForm()}}

        <div class="comment__conversation">
            @foreach($comments as $comment)
                @if($comment->comment_parent == 0)

                    <div class="parent" id="div-comment-{{$comment->comment_ID}}">

                        @if (is_user_logged_in())
                            <div class="comment--likes">
                                @button([
                                    'icon' => 'thumb_up',
                                    'size' => 'sm',
                                    'color' => '',
                                    'style' => 'basic',
                                    'type' => 'button',
                                    'classList' => ['comment--likes-icon', \Municipio\Comment\Likes::likeButton($comment->comment_ID)['classList']],
                                    'attributeList' => [
                                        'data-commentid' => $comment->comment_ID,
                                    ],
                                ])
                                @endbutton

                                <span data-likes="{{\Municipio\Comment\Likes::likeButton($comment->comment_ID)['count']}}"
                                      id="comment-likes-{{$comment->comment_ID}}">
                                        {{\Municipio\Comment\Likes::likeButton($comment->comment_ID)['count']}}
                                </span>
                            </div>
                        @endif

                        <a name="comment-{{$comment->comment_ID}}"></a>
                        @comment([
                            'author' => $comment->comment_author,
                            'author_url' => 'mailto:'.$comment->comment_author_email,
                            'author_image' => get_user_by('email', $comment->comment_author_email) ? get_avatar_url(get_user_by('email', $comment->comment_author_email)->data->ID ) : false,
                            'href' => '',
                            'text' => get_comment_text($comment->comment_ID),
                            'icon' => 'face',
                            'bubble_color' => 'dark',
                            'date_suffix' => __('ago', 'municipio'),
                            'date' => date('Y-m-d H:i', strtotime($comment->comment_date)),
                            'classList' => ['comment-'.$comment->comment_ID, 'comment-reply-link'],
                        ])
                        @endcomment
                    </div>

                    <div class="reply comment--actions">
                        @button([
                            'icon' => 'reply',
                            'reversePositions' => true,
                            'style' => 'basic',
                            'color' => 'secondary',
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
                    </div>

                    @if (!empty(get_comments(array('parent' => $comment->comment_ID, 'order' => 'asc'))))
                        @foreach(get_comments(array('parent' => $comment->comment_ID, 'order' => 'asc'))  as $answer)

                            <div class="child" id="div-comment-{{$answer->comment_ID}}">
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

                                        <span data-likes="{{\Municipio\Comment\Likes::likeButton($answer->comment_ID)['count']}}"
                                              id="comment-likes-{{$answer->comment_ID}}">
                                            {{\Municipio\Comment\Likes::likeButton($answer->comment_ID)['count']}}
                                        </span>
                                    </div>
                                @endif

                                <a name="comment-{{$answer->comment_ID}}"></a>
                                @comment([
                                    'author' => $answer->comment_author,
                                    'author_url' => 'mailto:'.$answer->comment_author_email,
                                    'author_image' => get_avatar_url(get_user_by('email', $answer->comment_author_email)->data->ID ),
                                    'href' => '',
                                    'text' => get_comment_text($answer->comment_ID),
                                    'icon' => 'face',
                                    'bubble_color' => 'dark',
                                    'date_suffix' => __('ago', 'municipio'),
                                    'date' => date('Y-m-d H:i', strtotime($answer->comment_date)),
                                    'is_reply' => true
                                ])
                                @endcomment
                            </div>
                        @endforeach
                    @endif
                @endif
            @endforeach
        </div>
    </div>
@endif