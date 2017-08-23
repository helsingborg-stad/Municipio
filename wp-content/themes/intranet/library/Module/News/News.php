<?php

namespace Intranet\Module;

class News extends \Modularity\Module
{
    public $slug = 'intranet-news';
    public $icon = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyOTcgMjk3Ij48cGF0aCBkPSJNMTczLjg1OCAxMDQuNjI2aDcwLjQxdjUyLjQzMmgtNzAuNDF6Ii8+PHBhdGggZD0iTTQ0LjY3NyAyNjIuNDNoMjQyLjI1NmM1LjU2IDAgMTAuMDY3LTQuNTEgMTAuMDY3LTEwLjA3VjQ0LjY0YzAtNS41Ni00LjUwOC0xMC4wNjgtMTAuMDY3LTEwLjA2OEg0NC42NzdjLTUuNTYgMC0xMC4wNjcgNC41MDgtMTAuMDY3IDEwLjA2OHYyMDcuNzJjMCA1LjU2IDQuNTA3IDEwLjA3IDEwLjA2NyAxMC4wN3pNMTU3Ljc1IDk2LjU3YTguMDU1IDguMDU1IDAgMCAxIDguMDU0LTguMDU1aDg2LjUyYTguMDU1IDguMDU1IDAgMCAxIDguMDU1IDguMDU1djY4LjU0YTguMDU1IDguMDU1IDAgMCAxLTguMDU2IDguMDU0aC04Ni41MmE4LjA1NSA4LjA1NSAwIDAgMS04LjA1NC04LjA1NHYtNjguNTR6bS03OC40NjYtOC4wNTRoNTEuOTEzYTguMDU1IDguMDU1IDAgMCAxIDAgMTYuMTFINzkuMjg0YTguMDU0IDguMDU0IDAgMSAxIDAtMTYuMTF6bTAgMzQuNjJoNTEuOTEzYTguMDU1IDguMDU1IDAgMCAxIDAgMTYuMTFINzkuMjg0YTguMDU1IDguMDU1IDAgMSAxIDAtMTYuMTF6bTAgMzQuNjE2aDUxLjkxM2E4LjA1NSA4LjA1NSAwIDAgMSAwIDE2LjExSDc5LjI4NGE4LjA1NSA4LjA1NSAwIDEgMSAwLTE2LjExem0wIDUxLjkzMmgxNzMuMDRhOC4wNTYgOC4wNTYgMCAwIDEgMCAxNi4xMUg3OS4yODNhOC4wNTUgOC4wNTUgMCAwIDEgMC0xNi4xMXpNMTguNSAyNTIuMzZWNjkuMTkyaC04LjQzM0M0LjUwNyA2OS4xOTIgMCA3My43IDAgNzkuMjYydjE3My4xYzAgNS41NiA0LjUwOCAxMC4wNjcgMTAuMDY3IDEwLjA2N2gxMC40NWEyNi4wNTUgMjYuMDU1IDAgMCAxLTIuMDE2LTEwLjA3eiIvPjwvc3ZnPg==';
    public $supports = array();
    public $hasImages = false;

    public $templateDir = INTRANET_TEMPLATE_PATH . 'module';

    public function init()
    {
        $this->nameSingular = __('News', 'municipio-intranet');
        $this->namePlural = __('News', 'municipio-intranet');
        $this->description = __('Shows news stories from the sites the current user is subscribing to (or from all if logged out)', 'municipio-intranet');
    }

    public function data() : array
    {
        $data = array();
        $limit = !empty(get_field('limit', $this->ID)) ? get_field('limit', $this->ID) : 10;
        $sites = $this->getSites();

        $data['display'] = get_field('display', $this->ID);
        $data['limit'] = $limit;
        $data['news'] = \Intranet\CustomPostType\News::getNews($limit, $sites);
        $data['helpTooltip'] = false;
        $data['module'] = $this->data;

        $data['sites'] = $sites;
        if (is_array($data['sites'])) {
            $data['sites'] = implode(',', $sites);
        }

        $this->preparePosts();
        $data['hasImages'] = $this->hasImages;

        $data['args'] = $this->args;

        $data['classes'] = implode(' ', apply_filters('Modularity/Module/Classes', array('box', 'box-news', 'box-news-horizontal'), $this->post_type, $this->args));

        $data['categoryDropdownArgs'] = array(
            'orderby' => 'name',
            'echo' => 0,
            'show_option_all' => __("Select category", 'municipio-intranet'),
            'hide_if_empty' => true
        );

        return $data;
    }

    public function preparePosts()
    {
        if (get_field('placeholders', $this->ID)) {
            if (is_array($news) && !empty($news)) {
                foreach ($news as $item) {
                    if (get_thumbnail_source($item->ID) !== false) {
                        $this->hasImages = true;
                    }
                }
            }
        }
    }

    public function getSites()
    {
        $display = get_field('display', $this->ID);

        switch ($display) {
            default:
            case 'network_subscribed':
                if (is_user_logged_in()) {
                    $sites = array_merge(
                        (array) \Intranet\User\Subscription::getForcedSubscriptions(true),
                        (array) \Intranet\User\Subscription::getSubscriptions(null, true)
                    );
                } else {
                    $sites = 'all';
                }

                break;

            case 'network':
                $sites = 'all';
                break;

            case 'blog':
                $sites = (array) get_current_blog_id();
                break;
        }

        return $sites;
    }

    /**
     * Available "magic" methods for modules:
     * init()            What to do on initialization (if you must, use __construct with care, this will probably break stuff!!)
     * data()            Use to send data to view (return array)
     * style()           Enqueue style only when module is used on page
     * script            Enqueue script only when module is used on page
     * adminEnqueue()    Enqueue scripts for the module edit/add page in admin
     * template()        Return the view template (blade) the module should use when displayed
     */
}
