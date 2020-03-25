<?php
$key = defined('G_RECAPTCHA_KEY') ? G_RECAPTCHA_KEY : '';
$reCaptcha = (!is_user_logged_in(
    0)) ? '<div class="g-recaptcha" data-sitekey="' . $key . '"></div></div>' : '';

ob_start();
echo '
<div class="comment-respond comment-respond-new u-mt-4">
    <textarea name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s"></textarea>
</div>
';


/*comment_form(array(
    'class_submit' 			=> 'btn btn-primary',
    'submit_button' 		=> $reCaptcha . '<div class="form-group"><input  /></div>'
));*/
ob_get_clean();
//echo str_replace('class="comment-respond"','class="comment-respond comment-respond-new u-mt-4"',
$current_user = wp_get_current_user();


$args = array(
    'id_form'           => 'commentForm',
    'class_form'        => 'c-form',
    'id_submit'         => 'submit',
    'class_submit'      => 'c-button c-button__filled c-button__filled--primary c-button--md',
    'name_submit'       => 'submit',
    'submit_button'     => '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />',
    'format'            => 'xhtml',
    'comment_field'     =>  $reCaptcha. '<div class="c-textarea"><textarea id="comment"
    name="comment" placeholder="'.__
        ('Comment
    text','text-domain').'" aria-required="true">' .'</textarea></div>',
    'comment_notes_before' => '<p class="comment-notes">' . __( 'Your email address will not be published.','text-domain' ) .'</p>'
);

comment_form( $args );

?>


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

        {{-- Comment Thread --}}
        @comment([
            'author' => $userName,
            'author_url' => 'mailto:'.$comment->comment_author_email,
            'author_image' => $userAvatar,
            'text' => get_comment_text($comment->comment_ID),
            'icon' => 'face',
            'date' => date('Y-m-d \k\l\. H:i', strtotime($comment->comment_date)),
            'classList' => ['comment-'.$comment->comment_ID]
        ])


            @if (\Municipio\Helper\Hash::short(\Municipio\Helper\Likes::likeButton
                    ($comment->comment_ID)) !== null )
                <span class="like">
                    {!! \Municipio\Helper\Hash::short(\Municipio\Helper\Likes::likeButton
                    ($comment->comment_ID)) !!}
                </span>
            @endif

        @endcomment

        @if (is_user_logged_in())

            <div class="u-padding__top--1 ">
                @button([
                    'icon' => 'reply',
                    'reversePositions' => true,
                    'style' => 'basic',
                    'color' => 'secondary',
                    'text' => __('Reply', 'municipio'),
                    'attributeList' => ['js-toggle-trigger' => 'reply-form-'.$comment->comment_ID],
                    'classList' => ['u-float--right']
                ])
                @endbutton
            </div>


            <div class="u-display--none" js-toggle-item="{{'reply-form-'
            .$comment->comment_ID}}"
                 js-toggle-class="u-display--none">
                @php
                    $args = array(
                        'id_form'           => 'commentForm',
                        'class_form'        => 'comment-form',
                        'id_submit'         => 'submit',
                        'class_submit'      => 'c-button c-button__filled
                        c-button__filled--secondary c-button--md',
                        'name_submit'       => 'submit',
                        'submit_button'     => '<input name="%1$s"
                            type="submit" id="%2$s"
                            class="%3$s"
                            value="'.__('Post comment', 'municipio').'" />',
                        'title_reply'       => '',
                        'title_reply_to'    => __( 'Reply to %s','text-domain' ),
                        'cancel_reply_link' => __( 'Cancel comment','text-domain' ),
                        'label_submit'      => __( 'Post comment','text-domain' ),
                        'format'            => 'xhtml',
                        'comment_field'     =>  $reCaptcha. '<div class="c-textarea">
                            <textarea id="comment" name="comment"
                            placeholder="'.__('Comment text','text-domain').'"
                            aria-required="true">' .'</textarea></div>',
                        'comment_notes_before' => '<p class="comment-notes">'
                            . __( 'Your email address will not be published.','text-domain' ) .'</p>'
                    );

                    comment_form( $args );
                @endphp

            </div>
        @endif

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

                        $userName = (get_user_by('email', $answers->comment_author_email)->data->user_nicename) ?
                            (get_user_by('email', $answers->comment_author_email)->data->user_nicename) : '';
                        $userAvatar = (get_user_by('email',$answers->comment_author_email) !== null &&
                            !empty(get_user_by('email',$answers->comment_author_email)))
                            ? $userAvatar = get_avatar_url(get_user_by('email', $answers->comment_author_email)->data->ID ) : '';

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

                        @if (\Municipio\Helper\Hash::short(\Municipio\Helper\Likes::likeButton
                            ($$answer->comment_ID)) !== null )

                                <span class="like">
                                {!! \Municipio\Helper\Hash::short(\Municipio\Helper\Likes::likeButton
                                ($answer->comment_ID)) !!}
                            </span>

                        @endif

                    @endcomment

            @endforeach
        @endif

    @endif
@endforeach