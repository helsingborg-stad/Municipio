<?php

if (!function_exists('intranet_has_subscribed')) {
    function intranet_has_subscribed($blog_id) {
        $subscriptionClass = new \Intranet\User\Subscription();
        $user_id = get_current_user_id();

        return $subscriptionClass->hasSubscribed($user_id, $blog_id);
    }
}
