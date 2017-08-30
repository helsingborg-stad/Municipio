<?php
    ob_start();
    comment_form(array(
        'class_submit' => 'btn btn-primary'
    ));
    echo str_replace('class="comment-respond"','class="comment-respond comment-respond-new"',ob_get_clean());
?>
