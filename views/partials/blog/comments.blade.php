<ul id="comments" class="comments">
    @foreach($comments as $comment)
        @if($comment->comment_parent == 0)
            <li class="comment" id="comment-{{$comment->comment_ID}}">
                <header>
                    <div class="author-image">
                        <a href="{{ municipio_get_user_profile_url($comment->comment_author_email) }}">
                            @if(get_the_author_meta('user_profile_picture', get_user_by('email', $comment->comment_author_email)->ID))
                            <img src="{{ get_the_author_meta('user_profile_picture', get_user_by('email', $comment->comment_author_email)->ID) }}">
                            @else
                            <i class="pricon pricon-2x pricon-user-o"></i>
                            @endif
                        </a>
                    </div>

                    <div class="author-name">
                        <a class="author-link" href="{{ municipio_get_user_profile_url($comment->comment_author_email) }}">
                            <em>{{ $comment->comment_author }}</em>
                        </a>
                    </div>

                    <time data-tooltip="{{ date('Y-m-d \k\l\. H:i', strtotime($comment->comment_date)) }}" data-tooltip-right datetime="{{ $comment->comment_date_gmt }}">
                        {{ municipio_human_datediff($comment->comment_date) }} sedan
                    </time>

                    <div class="reply">
                        {{comment_reply_link($replyArgs,$comment->comment_ID,$comment->comment_post_ID)}}
                    </div>
                </header>
                <div class="comment-text">
                    {!! comment_text($comment->comment_ID); !!}
                </div>

                @foreach($comments as $answer)
                    @if($answer->comment_parent == $comment->comment_ID)
                        <ul class="answers">
                            <li>
                                <header>
                                    <div class="author-image">
                                        <a href="{{ municipio_get_user_profile_url($answer->comment_author_email) }}">
                                            @if(get_the_author_meta('user_profile_picture', get_user_by('email', $comment->comment_author_email)->ID))
                                            <img src="{{ get_the_author_meta('user_profile_picture', get_user_by('email', $comment->comment_author_email)->ID) }}">
                                            @else
                                            <i class="pricon pricon-2x pricon-user-o"></i>
                                            @endif
                                        </a>
                                    </div>

                                    <div class="author-name">
                                        <a class="author-link" href="{{ municipio_get_user_profile_url($answer->comment_author_email) }}">
                                            <em>{{ $answer->comment_author }}</em>
                                        </a>
                                    </div>

                                    <time data-tooltip="{{ date('Y-m-d \k\l\. H:i', strtotime($answer->comment_date)) }}" data-tooltip-right datetime="{{ $answer->comment_date_gmt }}">
                                        {{ municipio_human_datediff($comment->comment_date) }} sedan
                                    </time>
                                </header>
                                <div class="comment-text">
                                    {!! comment_text($answer->comment_ID); !!}
                                </div>
                            </li>
                        </ul>
                    @endif
                @endforeach

            </li>
        @endif
    @endforeach
</ul>
