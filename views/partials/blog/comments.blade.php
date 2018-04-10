 <div class="comments-wrapper">
    <ul class="comments comments-new" id="comments">
        @foreach($comments as $comment)
            @if($comment->comment_parent == 0)
                <li class="comment comment-new" id="comment-{{$comment->comment_ID}}">
                    <div class="author-image">
                        @if (isset($authorPages) && $authorPages == true && email_exists($comment->comment_author_email) !== false)
                           <a href="{{ municipio_get_user_profile_url($comment->comment_author_email) }}">
                                @if(get_the_author_meta('user_profile_picture', get_user_by('email', $comment->comment_author_email)->ID))
                                    <img src="{{ get_the_author_meta('user_profile_picture', get_user_by('email', $comment->comment_author_email)->ID) }}">
                                @else
                                    <i class="pricon pricon-2x pricon-user-o"></i>
                                @endif
                            </a>
                        @else
                            <span>
                                <i class="pricon pricon-2x pricon-user-o"></i>
                            </span>
                        @endif
                    </div>
                    <div class="comment-body">
                        <div class="comment-header">
                                <em class="author-name">
                                    @if (isset($authorPages) && $authorPages == true && email_exists($comment->comment_author_email) !== false)
                                        <a href="{{ municipio_get_user_profile_url($comment->comment_author_email) }}">
                                            {{ get_user_by('email', $comment->comment_author_email)->display_name }}
                                        </a>
                                    @else
                                        <span>
                                            {{$comment->comment_author}}
                                        </span>
                                    @endif
                                </em>
                            <time data-tooltip="{{ date('Y-m-d \k\l\. H:i', strtotime($comment->comment_date)) }}" data-tooltip-right datetime="{{ $comment->comment_date_gmt }}">
                                {{ municipio_human_datediff($comment->comment_date) }} sedan
                            </time>
                        </div>
                        <div class="comment-content">
                            {!! comment_text($comment->comment_ID); !!}
                        </div>
                        <div class="comment-footer">
                            @if (Municipio\Controller\Single::likeButton($comment->comment_ID) !== null )
                            <span class="like">
                                {!! Municipio\Controller\Single::likeButton($comment->comment_ID) !!}
                            </span>
                            @endif
                            @if(isset($comment->comment_parent) && $comment->comment_parent == 0)
                                <span class="reply">
                                    {{comment_reply_link($replyArgs,$comment->comment_ID,$comment->comment_post_ID)}}
                                </span>
                            @endif
                            @if($comment->user_id == get_current_user_id())
                            <span class="actions">
                                <a href="#" data-dropdown=".comment-actions-{{ $comment->comment_ID }}">
                                    <i class="pricon pricon-menu-dots"></i>
                                </a>
                                <ul class="comment-actions-{{ $comment->comment_ID }} dropdown-menu dropdown-menu-arrow dropdown-menu-arrow-left">
                                    <li>
                                        <a href="#" id="delete-comment" data-comment-nonce="{{ wp_create_nonce("delete-comment_$comment->comment_ID") }}" data-comment-id="{{ $comment->comment_ID }}"><i class="pricon pricon-minus-o pricon-space-right"></i> <?php _e('Remove', 'municipio'); ?></a>
                                    </li>
                                </ul>
                            </span>
                            @endif
                        </div>
                    </div>

                    <?php $answers = get_comments(array('parent' => $comment->comment_ID, 'order' => 'asc')); ?>
                    @if (isset($answers) && $answers)
                        <ul class="answers">
                             @foreach($answers as $answer)
                                <li class="answer" id="answer-{{$answer->comment_ID}}">
                                    <div class="author-image">
                                        @if (isset($authorPages) && $authorPages == true && email_exists($answer->comment_author_email) !== false)
                                           <a href="{{ municipio_get_user_profile_url($answer->comment_author_email) }}">
                                                @if(get_the_author_meta('user_profile_picture', get_user_by('email', $answer->comment_author_email)->ID))
                                                    <img src="{{ get_the_author_meta('user_profile_picture', get_user_by('email', $answer->comment_author_email)->ID) }}">
                                                @else
                                                    <i class="pricon pricon-2x pricon-user-o"></i>
                                                @endif
                                            </a>
                                        @else
                                            <span>
                                                <i class="pricon pricon-2x pricon-user-o"></i>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="comment-body">
                                        <div class="comment-header">
                                            <em class="author-name">
                                                @if (isset($authorPages) && $authorPages == true && email_exists($answer->comment_author_email) !== false)
                                                    <a href="{{ municipio_get_user_profile_url($answer->comment_author_email) }}">
                                                        {{ get_user_by('email', $answer->comment_author_email)->display_name }}
                                                    </a>
                                                @else
                                                    <span>
                                                        {{$answer->comment_author}}
                                                    </span>
                                                @endif
                                            </em>
                                            <time data-tooltip="{{ date('Y-m-d \k\l\. H:i', strtotime($answer->comment_date)) }}" data-tooltip-right datetime="{{ $comment->comment_date_gmt }}">
                                                {{ municipio_human_datediff($answer->comment_date) }} sedan
                                            </time>
                                        </div>
                                        <div class="comment-content">
                                            {!! comment_text($answer->comment_ID); !!}
                                        </div>
                                        <div class="comment-footer">
                                            @if (Municipio\Controller\Single::likeButton($answer->comment_ID) !== null )
                                            <span class="like">
                                                {!! Municipio\Controller\Single::likeButton($answer->comment_ID) !!}
                                            </span>
                                            @endif
                                            @if($answer->user_id == get_current_user_id())
                                            <span class="actions">
                                                <a href="#" data-dropdown=".comment-actions-{{ $answer->comment_ID }}">
                                                    <i class="pricon pricon-menu-dots"></i>
                                                </a>
                                                <ul class="comment-actions-{{ $answer->comment_ID }} dropdown-menu dropdown-menu-arrow dropdown-menu-arrow-left">
                                                    <li>
                                                        <a href="#" id="delete-comment" data-comment-nonce="{{ wp_create_nonce("delete-comment_$answer->comment_ID") }}" data-comment-id="{{ $answer->comment_ID }}"><i class="pricon pricon-minus-o pricon-space-right"></i> <?php _e('Remove', 'municipio'); ?></a>
                                                    </li>
                                                </ul>
                                            </span>
                                            @endif
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
