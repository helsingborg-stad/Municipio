<?php

namespace Intranet\Helper;

class PostRank
{
    public static $baseRanks = array(
        'sticky'     => 1000,
        'mainBlog'   => 400,
        'forced'     => 300,
        'subscribed' => 200,
        'default'    => 100
    );

    public static $multipliers = array(
        'baseRank' => 1,
        'pageViews' => 20,
        'userViews' => 10,
        'age'       => 0.001
    );

    /**
     * Calculates the rank of a post
     * @param  WP_Post  $post        Wp post object
     * @param  boolean $useBaseRank  Wheater to use baseRank
     * @param  boolean $usePageViews Wheater to use pageViews
     * @param  boolean $useUserViews Wheater to use userViews
     * @param  boolean $useAge       Wheater to use age
     * @return integer               Rank score
     */
    public static function rank($post, $useBaseRank = true, $usePageViews = true, $useUserViews = true, $useAge = true)
    {
        $rank = 0;

        // Detect which $baseRanks key we should use for this post
        // Then calculate the baseRank
        $baseRank = 0;
        if ($useBaseRank) {
            $baseRankKey = self::getBaseRankKey($post);
            $baseRank = self::$baseRanks[$baseRankKey] * self::$multipliers['baseRank'];
        }

        // Page views
        $pageViews = 0;
        if ($usePageViews) {
            $pageViews = isset($post->page_views) ? $post->page_views : 0;
            $pageViews = $pageViews * self::$multipliers['pageViews'];
        }

        // Views by users
        $userViews = 0;
        if ($useUserViews) {
            $userViews = isset($post->user_views) ? $post->user_views : 0;
            $userViews = $userViews * self::$multipliers['userViews'];
        }

        // Age score
        $age = 0;
        if ($useAge) {
            $age = strtotime($post->post_date) * self::$multipliers['age'];
        }

        $rank = $baseRank + $pageViews + $userViews + $age;

        return (int) $rank;
    }

    /**
     * Get base rank key to use
     * @param  WP_Post $post Wp post object
     * @return string        Base rank key
     */
    public static function getBaseRankKey($post)
    {
        $baseRankKey = 'default';

        if ($post->is_sticky) {
            $baseRankKey = 'sticky';
        } elseif ($post->blog_id == BLOG_ID_CURRENT_SITE) {
            $baseRankKey = 'mainBlog';
        } elseif (\Intranet\User\Subscription::isForcedSubscription($post->blog_id)) {
            $baseRankKey = 'forced';
        } elseif (\Intranet\User\Subscription::hasSubscribed($post->blog_id)) {
            $baseRankKey = 'subscribed';
        }

        return $baseRankKey;
    }
}
