<?php

namespace Municipio\Controller;

class BaseController
{
    /**
     * Holds the view's data
     * @var array
     */
    protected $data = array();

    public function __construct()
    {
        $this->getLogotype();
        $this->getHeaderLayout();

        $this->init();
    }

    public function getLogotype()
    {
        $this->data['logotype'] = array(
            'standard' => get_field('logotype', 'option'),
            'negative' => get_field('logotype_negative', 'option')
        );
    }

    public function getHeaderLayout()
    {
        switch (get_field('header_layout', 'option')) {
            case 'casual':
                $this->data['headerLayout'] = 'views.partials.header.casual';
                break;

            default:
                $this->data['headerLayout'] = 'views.partials.header.business';
                break;
        }
    }

    /**
     * Runs after construct
     * @return void
     */
    public function init()
    {
        // Method body
    }

    /**
     * Bind to a custom template file
     * @return void
     */
    public static function registerTemplate()
    {
        // \Municipio\Helper\Template::add('Front page', 'front-page.blade.php');
    }

    /**
     * Returns the data
     * @return array Data
     */
    public function getData()
    {
        return apply_filters('HbgBlade/data', $this->data);
    }
}
