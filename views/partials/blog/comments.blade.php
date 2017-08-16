<ul id="comments" class="comments">
    @foreach($comments as $comment)
        @if($comment->comment_parent == 0)
            <li class="comment">
                <header>
                    <div class="author-image">
                        <a href="{{ municipio_get_user_profile_url($comment->user_id) }}">
                            @if(get_the_author_meta('user_profile_picture', $comment->user_id))
                            <img src="{{ get_the_author_meta('user_profile_picture', $comment->user_id) }}">
                            @else
                            <i class="pricon pricon-2x pricon-user-o"></i>
                            @endif
                        </a>
                    </div>

                    <div class="author-name">
                        <a class="author-link" href="{{ municipio_get_user_profile_url($comment->user_id) }}">
                            <em>{{ $comment->comment_author }}</em>
                        </a>
                    </div>

                    <time data-tooltip="{{ date('Y-m-d \k\l\. H:i', strtotime($comment->comment_date)) }}" data-tooltip-right datetime="{{ $comment->comment_date_gmt }}">
                        {{ municipio_human_datediff($comment->comment_date) }} sedan
                    </time>
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
                                        <a href="{{ municipio_get_user_profile_url($answer->user_id) }}">
                                            @if(get_the_author_meta('user_profile_picture', $answer->user_id))
                                            <img src="{{ get_the_author_meta('user_profile_picture', $answer->user_id) }}">
                                            @else
                                            <i class="pricon pricon-2x pricon-user-o"></i>
                                            @endif
                                        </a>
                                    </div>

                                    <div class="author-name">
                                        <a class="author-link" href="{{ municipio_get_user_profile_url($answer->user_id) }}">
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
