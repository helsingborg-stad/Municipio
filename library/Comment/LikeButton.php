<?php

namespace Municipio\Comment;

class LikeButton extends \Municipio\Helper\Ajax
{
    public function __construct() {
        $this->id = get_queried_object_id();

        //Data
        $this->data['ajax_url'] = admin_url( 'admin-ajax.php' );
        $this->data['nonce'] = wp_create_nonce( 'likeNonce' );

        //Localize
        $this->localize('likeButtonData');

        //Hook
        $this->hook('likeButton', true);
        //add_action('init', array($this, 'likeButton'));

    }

    public function likeButton()
    {

        ignore_user_abort(true);

        if ( ! defined( 'DOING_AJAX' ) && ! DOING_AJAX ) {
            return;
        }

        if (! wp_verify_nonce( $_POST['nonce'], 'likeNonce' ) ) {
            die ( 'Busted!');
        }

        $commentId = $_REQUEST['comment_id'];

        $like = array();

        if(is_array(get_comment_meta( $commentId, '_likes', true )) == true) {
            $like = array_merge($like, get_comment_meta( $commentId, '_likes', true ));
        }

        if(in_array(get_current_user_id(), $like)) {
            $index = array_search(get_current_user_id(), $like);
            unset($like[$index]);
        } else {
            $like[] = get_current_user_id();
        }

        update_comment_meta( $commentId, '_likes', $like );

        $likes = count($like);

        echo $likes;
        die();

    }
}
