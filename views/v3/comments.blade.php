<?php

#if (is_single() && comments_open() && get_option('comment_registration') == 0 || is_single() &&
# comments_open() && is_user_logged_in()) {

    $key = defined('G_RECAPTCHA_KEY') ? G_RECAPTCHA_KEY : '';
    $reCaptcha = (!is_user_logged_in(
        0)) ? '<div class="g-recaptcha" data-sitekey="' . $key . '"></div></div>' : '';

    ob_start();
    ob_get_clean();

    $current_user = wp_get_current_user();

    $args = array(
        'id_form'           => 'commentform',
        'logged_in_as'      => '',
        'must_log_in'       => '',
        'class_form'        => 'c-form',
        'id_submit'         => 'submit',
        'class_submit'      => 'c-button u-float--right comment-reply-link c-button__basic c-button__basic--secondary c-button--md',
        'name_submit'       => 'submit',
        'submit_button'     => '<div class="comment--actions">
            <input name="%1$s" type="submit" id="%2$s" class="u-padding__top--1
            u-padding__bottom--3%3$s" value="%4$s" /></div>',
        'format'            => 'html5',
        'cancel_reply_link' => __( 'Cancel reply' ),
        'comment_field'     =>  $reCaptcha. '<div class="c-textarea"><textarea id="comment"
        name="comment" placeholder="'.__
            ('Comment
        text','text-domain').'" aria-required="true">' .'</textarea></div>'
    );


    comment_form( $args );

?>

<div class="comment">
@foreach($comments as $comment)
    @if($comment->comment_parent == 0)

        @if ($comment->comment_author_email)

            @php

                $userName = (get_user_by('email', $comment->comment_author_email)) ?
                    (get_user_by('email', $comment->comment_author_email)->data->user_nicename) : '';
                $userAvatar = (get_user_by('email',$comment->comment_author_email) !== null &&
                    !empty(get_user_by('email',$comment->comment_author_email)))
                    ? $userAvatar = get_avatar_url(get_user_by('email', $comment->comment_author_email)->data->ID ) : '';

            @endphp

        @endif
        @if (\Municipio\Comment\Likes::likeButton($comment->comment_ID) !== null )
            @php
                $likeButton = \Municipio\Comment\Likes::likeButton($comment->comment_ID);
                $likButtonIcon = (strpos($likeButton['classList'], 'active')) ? 'thumb_down' :
                    'thumb_up';
                $likeButtonTxt = (strpos($likeButton['classList'], 'active')) ? __('Dislike ',
                'municipio') : __('Like ', 'municipio');
            @endphp
        @endif

        <div class="even parent" id="div-comment-{{$comment->comment_ID}}">
            <a name="comment--id-{{$comment->comment_ID}}"></a>
            {{-- Comment Thread --}}
            @comment([
                'author' => $userName,
                'author_url' => 'mailto:'.$comment->comment_author_email,
                'author_image' => $userAvatar,
                'text' => get_comment_text($comment->comment_ID),
                'icon' => 'face',
                'date' => date('Y-m-d \k\l\. H:i', strtotime($comment->comment_date)),
                'classList' => ['comment-'.$comment->comment_ID, 'comment-reply-link']
            ])

            @endcomment

        </div>

        @if (is_user_logged_in())

            <div class="reply comment--actions">
                <span class="comment--likes">
                    @icon([
                        'icon' => 'thumb_up',
                        'size' => 'sm',
                        'classList' => ['comment--likes-icon']
                    ])
                    @endicon

                    <span data-likes="{{$likeButton['count']}}"
                          id="comment-likes-{{$comment->comment_ID}}">
                            {{$likeButton['count']}}</span>

                </span>

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
                    'classList' => ['u-float--right', 'comment-reply-link']
                ])
                @endbutton

                @button([
                    'icon' => $likButtonIcon,
                    'reversePositions' => true,
                    'style' => 'basic',
                    'color' => 'secondary',
                    'text' => $likeButtonTxt,
                    'componentElement' => 'div',
                    'attributeList' => [
                    'data-commentid' => $comment->comment_ID,

                ],
                    'classList' => ['u-float--right', $likeButton['classList']]

                ])
                @endbutton

            </div>
        @endif

        @php
            $answers = get_comments(array('parent' => $comment->comment_ID, 'order' => 'asc'));
        @endphp

        {{-- COMMENTS ANSWERS --}}
        @if (isset($answers) && $answers)
            @foreach($answers as $answer)

                <a name="comment-{{$answer->comment_ID}}"></a>

                @if (isset($authorPages) && $authorPages == true && email_exists($answer->comment_author_email) !== false)
                    @php
                        $displayNameAnswer = get_user_by('email', $answer->comment_author_email);
                    @endphp
                @else

                    @php

                        $displayNameAnswer = (get_user_by('email', $answer->comment_author_email)) ?
                            (get_user_by('email', $answer->comment_author_email)->data->user_nicename) : '';
                        $userAvatarAnswer = (get_user_by('email',$answer->comment_author_email) !== null &&
                            !empty(get_user_by('email',$answer->comment_author_email)))
                            ? $userAvatar = get_avatar_url(get_user_by('email', $answer->comment_author_email)->data->ID ) : '';

                    @endphp

                @endif

                @comment([
                    'author' => $displayNameAnswer,
                    'author_url' => $answer->comment_author_email,
                    'author_image' => $userAvatarAnswer,
                    'text' => get_comment_text($answer->comment_ID),
                    'icon' => 'face',
                    'date' => date('Y-m-d \k\l\. H:i', strtotime($answer->comment_date)),
                    'is_reply' => true
                ])

                @endcomment

                @if (is_user_logged_in())

                    @if (\Municipio\Comment\Likes::likeButton($answer->comment_ID) !== null )
                        @php
                            $likeButtonAnswer = \Municipio\Comment\Likes::likeButton($answer->comment_ID);
                            $likButtonIconAnswer = (strpos($likeButtonAnswer['classList'], 'active')) ?
                            'thumb_down' : 'thumb_up';
                            $likeButtonTxtAnswer = (strpos($likeButtonAnswer['classList'], 'active')) ?
                                __('Dislike ','municipio') : __('Like ', 'municipio');
                        @endphp
                    @endif

                    <div class="reply comment--actions">
                        <span class="comment--likes-answer">
                            @icon([
                                'icon' => 'thumb_up',
                                'size' => 'sm',
                                'classList' => ['comment--likes-icon']
                            ])
                            @endicon

                            <span data-likes="{{$likeButtonAnswer['count']}}"
                                  id="comment-likes-{{$answer->comment_ID}}">
                                    {{$likeButtonAnswer['count']}}</span>

                            </span>

                            @button([
                                'icon' => $likButtonIconAnswer,
                                'reversePositions' => true,
                                'style' => 'basic',
                                'color' => 'secondary',
                                'text' => $likeButtonTxtAnswer,
                                'componentElement' => 'div',
                                'attributeList' => [
                                    'data-commentid' => $answer->comment_ID,

                                ],
                                'classList' => ['u-float--right', $likeButtonAnswer['classList']]

                            ])
                            @endbutton

                    </div>
                @endif
            @endforeach
        @endif

    @endif
@endforeach
</div>