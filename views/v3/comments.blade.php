@segment([
    'template' => 'featured',
    'containContent' => true,
    'height' => 'md',
    'width' => 'lg',
    'card' => [
        'isCard' => true,
        'background' => "gray",
        'padding' => "10"
    ],
    'text_alignment' => 'right',
    'content_alignment' => [
        'vertical' => 'center',
        'horizontal' => 'right'
    ],
    'article_heading' => [
        "variant" => "h1",
        "element" => "h2",
        "slot" => ""
    ],
        'article_body' => ""
])

@php

@endphp

@foreach($comments as $comment)
    @if($comment->comment_parent == 0)


        @if (isset($authorPages) && $authorPages == true &&
            email_exists($comment->comment_author_email) !== false)

            @php
                $userName = get_user_by('email', $comment->comment_author_email);

                $userAvatar = (get_the_author_meta('user_profile_picture', get_user_by('email',
                $comment->comment_author_email)->ID)) ?
                    get_the_author_meta('user_profile_picture', get_user_by('email', $comment->comment_author_email)->ID)
                 :
                    $userName
                 ;

            @endphp
        @else

            @php
                $userName =  $comment->comment_author;
            @endphp

        @endif

        {{-- Comment Thread --}}
        @comment([
            'author' => $userName,
            'text' => comment_text($comment->comment_ID),
            'icon' => 'face',
            'image' => 'https://picsum.photos/70/70?image=64',
            'date' => date('Y-m-d \k\l\. H:i', strtotime($comment->comment_date))
        ])


            @if (\Municipio\Helper\Hash::short(\Municipio\Helper\Likes::likeButton
                    ($comment->comment_ID)) !== null )
                <span class="like">
                    {!! \Municipio\Helper\Hash::short(\Municipio\Helper\Likes::likeButton
                    ($comment->comment_ID)) !!}
                </span>
            @endif

        @endcomment

        @php
            $answers = get_comments(array('parent' => $comment->comment_ID, 'order' => 'asc'));
        @endphp

        {{-- COMMENTS ANSWERS --}}
        @if (isset($answers) && $answers)
            @foreach($answers as $answer)

                @if (isset($authorPages) && $authorPages == true && email_exists($answer->comment_author_email) !== false)
                    @php
                        $displayNameAnswer = get_user_by('email', $answer->comment_author_email);
                    @endphp
                @else

                    @php
                        $displayNameAnswer =  $answer->comment_author;
                        $userAvatarAnswer = (get_the_author_meta('user_profile_picture', get_user_by('email',
                        $answer->comment_author_email)->ID)) ?
                            get_the_author_meta('user_profile_picture', get_user_by('email', $answer->comment_author_email)->ID)
                         :
                            $displayNameAnswer
                         ;
                    @endphp
                @endif

                    @comment([
                        'author' => $displayNameAnswer,
                        'text' => comment_text($answer->comment_ID),
                        'icon' => 'face',
                        'image' => $userAvatarAnswer,
                        'date' => date('Y-m-d \k\l\. H:i', strtotime($answer->comment_date)),
                        'is_reply' => true
                    ])
                    @endcomment

            @endforeach
        @endif

    @endif
@endforeach

@endsegment
