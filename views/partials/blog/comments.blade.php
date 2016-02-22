<ul id="comments" class="comments">
    @foreach($comments as $comment)
        <li class="comment">
            <header><em>{{ $comment->comment_author }}</em> <time data-tooltip="{{ date('Y-m-d \k\l\. H:i', strtotime($comment->comment_date)) }}" data-tooltip-right datetime="{{ $comment->comment_date_gmt }}">{{ municipio_human_datediff($comment->comment_date) }} sedan</time></header>
            <div class="comment-text">
                {{ $comment->comment_content }}
            </div>
        </li>
    @endforeach
</ul>
