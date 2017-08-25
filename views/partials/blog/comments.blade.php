<div class="comments-wrapper">
    <ul class="comments" id="comments">
        @foreach($comments as $comment)
            @if($comment->comment_parent == 0)
                <li class="comment" id="comment-{{$comment->comment_ID}}">
                    <div class="author-image">
                       <a href="{{ municipio_get_user_profile_url($comment->comment_author_email) }}">
                            @if(get_the_author_meta('user_profile_picture', get_user_by('email', $comment->comment_author_email)->ID))
                                <img src="{{ get_the_author_meta('user_profile_picture', get_user_by('email', $comment->comment_author_email)->ID) }}">
                            @else
                                <i class="pricon pricon-2x pricon-user-o"></i>
                            @endif
                        </a>
                    </div>
                    <div class="comment-body">
                        <div class="comment-header">
                                <em class="author-name">
                                    <a href="{{ municipio_get_user_profile_url($comment->comment_author_email) }}">
                                        {{ get_user_by('email', $comment->comment_author_email)->display_name }}
                                    </a>
                                </em>
                            <time data-tooltip="{{ date('Y-m-d \k\l\. H:i', strtotime($comment->comment_date)) }}" data-tooltip-right datetime="{{ $comment->comment_date_gmt }}">
                                {{ municipio_human_datediff($comment->comment_date) }} sedan
                            </time>
                        </div>
                        <div class="comment-content">
                            {!! comment_text($comment->comment_ID); !!}
                        </div>
                        <div class="comment-footer">
                            <span class="like">
                                {!! Municipio\Controller\Single::likeButton($comment->comment_ID) !!}
                            </span>
                            <span class="reply">
                                {{comment_reply_link($replyArgs,$comment->comment_ID,$comment->comment_post_ID)}}
                            </span>
                        </div>
                    </div>

                    <?php $answers = get_comments(array('parent' => $comment->comment_ID)); ?>
                    @if (isset($answers) && $answers)
                        <ul class="answers">
                             @foreach($answers as $answer)
                                <li class="answer" id="answer-{{$answer->comment_ID}}">
                                    <div class="author-image">
                                        <a href="{{ municipio_get_user_profile_url($answer->comment_author_email) }}">
                                            @if(get_the_author_meta('user_profile_picture', get_user_by('email', $answer->comment_author_email)->ID))
                                                <img src="{{ get_the_author_meta('user_profile_picture', get_user_by('email', $answer->comment_author_email)->ID) }}">
                                            @else
                                                <i class="pricon pricon-2x pricon-user-o"></i>
                                            @endif
                                        </a>
                                    </div>
                                    <div class="comment-body">
                                        <div class="comment-header">
                                            <em class="author-name">
                                                <a href="{{ municipio_get_user_profile_url($answer->comment_author_email) }}">
                                                    {{ $answer->comment_author }}
                                                </a>
                                            </em>
                                            <time data-tooltip="{{ date('Y-m-d \k\l\. H:i', strtotime($answer->comment_date)) }}" data-tooltip-right datetime="{{ $answer->comment_date_gmt }}">
                                                {{ municipio_human_datediff($answer->comment_date) }} sedan
                                            </time>
                                        </div>
                                        <div class="comment-content">
                                            {!! comment_text($answer->comment_ID); !!}
                                        </div>
                                        <div class="comment-footer">
                                            <span class="like">
                                                {!! Municipio\Controller\Single::likeButton($answer->comment_ID) !!}
                                            </span>
                                            <span class="reply">
                                                {{comment_reply_link($replyArgs,$comment->comment_ID,$comment->comment_post_ID)}}
                                            </span>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endif
        @endforeach
    </ul>
</div>
