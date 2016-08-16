<ul id="comments" class="comments">
    @foreach($comments as $comment)

        @if($comment->comment_parent == 0)
            <li class="comment">
                <header>
                    <em>{{ $comment->comment_author }}</em>
                    <time data-tooltip="{{ date('Y-m-d \k\l\. H:i', strtotime($comment->comment_date)) }}" data-tooltip-right datetime="{{ $comment->comment_date_gmt }}">
                        {{ municipio_human_datediff($comment->comment_date) }} sedan
                    </time>
                </header>
                <div class="comment-text">
                    {{ $comment->comment_content }}
                </div>

                @foreach($comments as $answer)
                    @if($answer->comment_parent == $comment->comment_ID)
                        <ul class="answers">
                            <li>
                                <header>
                                    <em>{{ $answer->comment_author }}</em>
                                    <time data-tooltip="{{ date('Y-m-d \k\l\. H:i', strtotime($answer->comment_date)) }}" data-tooltip-right datetime="{{ $answer->comment_date_gmt }}">
                                        {{ municipio_human_datediff($answer->comment_date) }} sedan
                                    </time>
                                </header>
                                <div class="comment-text">
                                    {{ $answer->comment_content }}
                                </div>
                            </li>
                        </ul>
                    @endif
                @endforeach

            </li>
        @endif
    @endforeach
</ul>
