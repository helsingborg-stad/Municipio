<?php

namespace Intranet\Search;

class Elasticsearch
{
    public static $level = 'all';

    public function __construct()
    {
        if (isset($_GET['level']) && !empty($_GET['level'])) {
            self::$level = sanitize_text_field($_GET['level']);
        }

        add_action('pre_get_posts', array($this, 'setSites'));
    }

    public function setSites($query)
    {
        switch (self::$level) {
            case 'subscriptions':
                $sites = array_merge(
                    \Intranet\User\Subscription::getSubscriptions(get_current_user_id(), true),
                    \Intranet\User\Subscription::getForcedSubscriptions(true)
                );
                $query->query_vars['sites'] = $sites;
                break;

            case 'current':
                $query->query_vars['sites'] = 'current';
                break;

            default:
                $query->query_vars['sites'] = 'all';
                break;
        }
    }
}
